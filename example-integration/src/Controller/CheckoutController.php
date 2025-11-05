<?php

namespace OmniKassa\ExampleIntegration\Controller;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Address;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\CustomerInformation;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\OrderItem;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\PaymentBrand;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\PaymentBrandForce;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request\MerchantOrder;
use OmniKassa\ExampleIntegration\Service\Service\Contract\OmniKassaClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    private OmniKassaClientInterface $omniKassaClient;
    private string $merchantReturnUrl;

    public function __construct(OmniKassaClientInterface $omniKassaClient, string $merchantReturnUrl)
    {
        $this->omniKassaClient = $omniKassaClient;
        $this->merchantReturnUrl = $merchantReturnUrl;
    }

    #[Route('/fast-checkout/process', name: 'fast_checkout_process', methods: ['POST'])]
    public function fastCheckout(Request $request): Response
    {
        $orderData = $this->getFastCheckoutOrderData();

        return $this->processCheckout($request, $orderData, 'Unable to process fast checkout');
    }

    #[Route('/sample-checkout', name: 'sample_checkout')]
    public function sampleCheckout(): Response
    {
        $items = $this->getOrderItems();
        $customer = new CustomerInformation('john.doe@example.com', '01-01-1980', 'M', 'JD', '+31201234567', 'John Doe');
        $address = $this->getAddress('Koopmans');

        $subtotal = $this->calculateTotal($items);
        $subtotalFormatted = number_format($subtotal / 100, 2);
        $totalFormatted = $subtotalFormatted; // No shipping cost for sample checkout

        return $this->render('home/checkout.html.twig', [
            'items' => $items,
            'customer' => $customer,
            'shipping' => $address,
            'billing' => $address,
            'subtotal' => $subtotalFormatted,
            'total' => $totalFormatted,
        ]);
    }

    #[Route('/sample-checkout/process', name: 'sample_checkout_process', methods: ['POST'])]
    public function processSampleCheckout(Request $request): Response
    {
        $orderData = $this->getSampleCheckoutOrderData($request);

        return $this->processCheckout($request, $orderData, 'Unable to process sample checkout');
    }

    private function getFastCheckoutOrderData(): array
    {
        $items = $this->getOrderItems();
        $customer = new CustomerInformation('fast@example.com', '01-01-1990', 'M', 'F', '+31123456789', 'Fast User');
        $address = $this->getAddress('Fast');

        return [
            'items' => $items,
            'customer' => $customer,
            'shipping' => $address,
            'billing' => $address,
            'description' => 'Fast checkout order',
            'paymentBrand' => PaymentBrand::IDEAL,
            'paymentBrandForce' => PaymentBrandForce::FORCE_ALWAYS,
        ];
    }

    private function getSampleCheckoutOrderData(Request $request): array
    {
        $items = $this->getOrderItems();
        $customer = new CustomerInformation('john.doe@example.com', '01-01-1990', 'M', 'JD', '+31201234567', 'John Doe');
        $address = $this->getAddress('Normal');

        $selectedPaymentBrand = $request->request->get('paymentBrand');
        $paymentBrand = !empty($selectedPaymentBrand) ? $selectedPaymentBrand : null;
        $paymentBrandForce = $paymentBrand ? PaymentBrandForce::FORCE_ONCE : null;

        return [
            'items' => $items,
            'customer' => $customer,
            'shipping' => $address,
            'billing' => $address,
            'description' => 'Sample checkout order',
            'paymentBrand' => $paymentBrand,
            'paymentBrandForce' => $paymentBrandForce,
            'selectedPaymentBrand' => $selectedPaymentBrand,
        ];
    }

    private function getOrderItems(): array
    {
        return [
            new OrderItem('Product X', 'A useful product', 2, Money::fromCents('EUR', 1999), Money::fromCents('EUR', 3998), 'tools'),
            new OrderItem('Gadget', 'A fancy gadget', 1, Money::fromCents('EUR', 2999), Money::fromCents('EUR', 2999), 'electronics'),
        ];
    }

    private function createMerchantOrder(array $items, string $description, Address $shipping, Address $billing, CustomerInformation $customer, ?string $paymentBrand = null, ?string $paymentBrandForce = null): MerchantOrder
    {
        $total = array_reduce($items, function ($sum, $item) {
            return $sum + ($item->getAmount()->getAmount() * $item->getQuantity());
        }, 0);
        $totalMoney = Money::fromCents('EUR', $total);

        return new MerchantOrder(
            bin2hex(random_bytes(10)),
            $description,
            $items,
            $totalMoney,
            $shipping,
            'nl',
            $this->merchantReturnUrl,
            $paymentBrand,
            $paymentBrandForce,
            $customer,
            $billing
        );
    }

    private function getAddress(string $lastName): Address
    {
        return new Address(
            'Albert',
            null,
            $lastName,
            'Straatnaam',
            '1011 AB',
            'Amsterdam',
            'NL',
            '12'
        );
    }

    private function processCheckout(Request $request, array $orderData, string $errorPrefix): Response
    {
        $merchantOrder = $this->createMerchantOrder(
            $orderData['items'],
            $orderData['description'],
            $orderData['shipping'],
            $orderData['billing'],
            $orderData['customer'],
            $orderData['paymentBrand'],
            $orderData['paymentBrandForce']
        );

        try {
            $orderResponse = $this->omniKassaClient->announceOrder($merchantOrder);

            // Store order ID in session, just for example purposes
            $request->getSession()->set('omnikassaOrderId', $orderResponse->getOmnikassaOrderId());

            return new RedirectResponse($orderResponse->getRedirectUrl());
        } catch (\Exception $e) {
            $subtotal = $this->calculateTotal($orderData['items']);
            $subtotalFormatted = number_format($subtotal / 100, 2);
            $totalFormatted = $subtotalFormatted; // No shipping cost in error case
            $paymentBrands = null;
            try {
                $paymentBrands = $this->omniKassaClient->getAllPaymentBrands();
            } catch (\Exception $e2) {
                // Ignore
            }

            $templateData = [
                'items' => $orderData['items'],
                'customer' => $orderData['customer'],
                'shipping' => $orderData['shipping'],
                'billing' => $orderData['billing'],
                'subtotal' => $subtotalFormatted,
                'total' => $totalFormatted,
                'paymentBrands' => $paymentBrands,
                'error' => $errorPrefix.': '.$e->getMessage(),
            ];

            // Add selected payment brand if it exists
            if (isset($orderData['selectedPaymentBrand'])) {
                $templateData['selectedPaymentBrand'] = $orderData['selectedPaymentBrand'];
            }

            return $this->render('home/checkout.html.twig', $templateData);
        }
    }

    private function calculateTotal(array $items): int
    {
        return array_reduce($items, function ($sum, $item) {
            return $sum + ($item->getAmount()->getAmount() * $item->getQuantity());
        }, 0);
    }
}
