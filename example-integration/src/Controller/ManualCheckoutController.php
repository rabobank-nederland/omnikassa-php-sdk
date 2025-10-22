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

class ManualCheckoutController extends AbstractController
{
    private OmniKassaClientInterface $omniKassaClient;
    private string $merchantReturnUrl;

    public function __construct(OmniKassaClientInterface $omniKassaClient, string $merchantReturnUrl)
    {
        $this->omniKassaClient = $omniKassaClient;
        $this->merchantReturnUrl = $merchantReturnUrl;
    }

    #[Route('/fast-manual-checkout/process', name: 'fast_manual_checkout_process', methods: ['POST'])]
    public function fastManualCheckout(Request $request): Response
    {
        $items = [
            new OrderItem('Fast Manual Product', 'A quick manual product', 1, Money::fromCents('EUR', 499), Money::fromCents('EUR', 499), 'books'),
        ];
        $customer = new CustomerInformation('fastmanual@example.com', '05-05-1995', 'F', 'FM', '+31987654321', 'Fast Manual User');
        $shipping = new Address(
            'FastManual',
            null,
            'User',
            'Manual Street',
            '5678 CD',
            'Manual City',
            'NL',
            '2'
        );
        $billing = new Address(
            'FastManual',
            null,
            'User',
            'Manual Street',
            '5678 CD',
            'Manual City',
            'NL',
            '2'
        );

        $totalAmount = 4.99;
        $totalMoney = Money::fromCents('EUR', (int) ($totalAmount * 100));

        $merchantOrder = new MerchantOrder(
            bin2hex(random_bytes(8)),
            'Fast manual checkout order',
            $items,
            $totalMoney,
            $shipping,
            'nl',
            $this->merchantReturnUrl,
            PaymentBrand::IDEAL,
            PaymentBrandForce::FORCE_ALWAYS,
            $customer,
            $billing
        );

        try {
            $result = $this->omniKassaClient->announceOrder($merchantOrder);
            $redirectUrl = $result['redirectUrl'];
            // Store order ID in session for potential later use
            $request->getSession()->set('omnikassaOrderId', $result['omnikassaOrderId']);

            return new RedirectResponse($redirectUrl);
        } catch (\Exception $e) {
            $totalFormatted = number_format($totalAmount, 2);

            return $this->render('home/checkout.html.twig', [
                'items' => $items,
                'customer' => $customer,
                'shipping' => $shipping,
                'total' => $totalFormatted,
                'error' => 'Unable to process fast manual checkout: '.$e->getMessage(),
            ]);
        }
    }

    #[Route('/manual-checkout', name: 'manual_checkout')]
    public function manualCheckout(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            // Process form data and show checkout confirmation
            $customer = new CustomerInformation(
                $request->request->get('email'),
                $request->request->get('dateOfBirth'),
                $request->request->get('gender'),
                $request->request->get('initials'),
                $request->request->get('phone'),
                $request->request->get('fullName')
            );

            $useCustomerAsShipping = 'on' === $request->request->get('useCustomerAsShipping');

            if ($useCustomerAsShipping) {
                // Use customer information for shipping address
                $nameParts = explode(' ', $request->request->get('fullName'), 2);
                $firstName = $nameParts[0] ?? '';
                $lastName = $nameParts[1] ?? $firstName; // Fallback if no last name

                $shipping = new Address(
                    $firstName,
                    null,
                    $lastName,
                    'Customer Address', // Placeholder since we don't have actual address
                    '0000 AA',
                    'Customer City',
                    'NL',
                    '1'
                );
            } else {
                // Use shipping form data
                $shipping = new Address(
                    $request->request->get('firstName'),
                    $request->request->get('middleName'),
                    $request->request->get('lastName'),
                    $request->request->get('street'),
                    $request->request->get('postalCode'),
                    $request->request->get('city'),
                    $request->request->get('countryCode'),
                    $request->request->get('houseNumber')
                );
            }

            // Process dynamic items
            $items = [];
            $itemsData = $request->request->all('items');
            if ($itemsData) {
                foreach ($itemsData as $itemData) {
                    $quantity = (int) $itemData['quantity'];
                    $unitPrice = (float) $itemData['unitPrice'];

                    $items[] = new OrderItem(
                        $itemData['name'],
                        $itemData['description'] ?? '',
                        $quantity,
                        Money::fromCents('EUR', (int) ($unitPrice * 100)),
                        Money::fromCents('EUR', (int) ($unitPrice * $quantity * 100)),
                        $itemData['taxCategory']
                    );
                }
            }

            $subtotal = array_reduce($items, function ($sum, $item) {
                return $sum + ($item->getAmount()->getAmount() * $item->getQuantity());
            }, 0);
            $subtotalFormatted = number_format($subtotal / 100, 2);
            $shippingCost = (float) $request->request->get('shippingCost', 0);
            $total = $subtotal + ($shippingCost * 100);
            $totalFormatted = number_format($total / 100, 2);

            return $this->render('home/checkout.html.twig', [
                'items' => $items,
                'customer' => $customer,
                'shipping' => $shipping,
                'subtotal' => $subtotalFormatted,
                'shippingCost' => $shippingCost,
                'total' => $totalFormatted,
            ]);
        }

        // Display form
        $paymentBrands = null;
        try {
            $paymentBrands = $this->omniKassaClient->getAllPaymentBrands();
        } catch (\Exception $e) {
            // Ignore, will show error in template if needed
        }

        return $this->render('home/manual_checkout.html.twig', [
            'paymentBrands' => $paymentBrands,
        ]);
    }

    #[Route('/manual-checkout/process', name: 'manual_checkout_process', methods: ['POST'])]
    public function processManualCheckout(Request $request): Response
    {
        // Process form data
        $customer = new CustomerInformation(
            $request->request->get('email'),
            $request->request->get('dateOfBirth'),
            $request->request->get('gender'),
            $request->request->get('initials'),
            $request->request->get('phone'),
            $request->request->get('fullName')
        );

        $useCustomerAsShipping = 'on' === $request->request->get('useCustomerAsShipping');

        if ($useCustomerAsShipping) {
            // Use customer information for shipping address
            $nameParts = explode(' ', $request->request->get('fullName'), 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? $firstName; // Fallback if no last name

            $shipping = new Address(
                $firstName,
                null,
                $lastName,
                'Customer Address', // Placeholder since we don't have actual address
                '0000 AA',
                'Customer City',
                'NL',
                '1'
            );
        } else {
            // Use shipping form data
            $shipping = new Address(
                $request->request->get('firstName'),
                $request->request->get('middleName'),
                $request->request->get('lastName'),
                $request->request->get('street'),
                $request->request->get('postalCode'),
                $request->request->get('city'),
                $request->request->get('countryCode'),
                $request->request->get('houseNumber')
            );
        }

        $billing = new Address(
            $request->request->get('firstName'),
            $request->request->get('middleName'),
            $request->request->get('lastName'),
            $request->request->get('street'),
            $request->request->get('postalCode'),
            $request->request->get('city'),
            $request->request->get('countryCode'),
            $request->request->get('houseNumber')
        );

        // Process dynamic items
        $items = [];
        $subtotalAmount = 0;
        $itemsData = $request->request->all('items');
        if ($itemsData) {
            foreach ($itemsData as $itemData) {
                $quantity = (int) $itemData['quantity'];
                $unitPrice = (float) $itemData['unitPrice'];
                $totalPrice = $quantity * $unitPrice;
                $subtotalAmount += $totalPrice;

                $items[] = new OrderItem(
                    $itemData['name'],
                    $itemData['description'] ?? '',
                    $quantity,
                    Money::fromCents('EUR', (int) ($unitPrice * 100)),
                    Money::fromCents('EUR', (int) ($totalPrice * 100)),
                    $itemData['taxCategory']
                );
            }
        }

        $shippingCost = (float) $request->request->get('shippingCost', 0);
        $totalAmount = $subtotalAmount + $shippingCost;
        $totalMoney = Money::fromCents('EUR', (int) ($totalAmount * 100));

        $merchantOrder = new MerchantOrder(
            bin2hex(random_bytes(8)),
            'Manual checkout order',
            $items,
            $totalMoney,
            $shipping,
            'nl',
            $this->merchantReturnUrl,
            PaymentBrand::IDEAL,
            PaymentBrandForce::FORCE_ALWAYS,
            $customer,
            $billing
        );

        try {
            $result = $this->omniKassaClient->announceOrder($merchantOrder);
            $redirectUrl = $result['redirectUrl'];
            // Store order ID in session for potential later use
            $request->getSession()->set('omnikassaOrderId', $result['omnikassaOrderId']);

            return new RedirectResponse($redirectUrl);
        } catch (\Exception $e) {
            $subtotalFormatted = number_format($subtotalAmount / 100, 2);
            $totalFormatted = number_format($totalAmount / 100, 2);

            return $this->render('home/checkout.html.twig', [
                'items' => $items,
                'customer' => $customer,
                'shipping' => $shipping,
                'subtotal' => $subtotalFormatted,
                'shippingCost' => $shippingCost,
                'total' => $totalFormatted,
                'error' => 'Unable to process manual checkout: '.$e->getMessage(),
            ]);
        }
    }
}
