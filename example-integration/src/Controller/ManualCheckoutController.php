<?php

namespace OmniKassa\ExampleIntegration\Controller;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Address;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\CustomerInformation;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\OrderItem;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\PaymentBrandMetaData;
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

    #[Route('/manual-checkout', name: 'manual_checkout', methods: ['GET'])]
    public function manualCheckout(Request $request): Response
    {
        $paymentBrands = $this->omniKassaClient->getAllPaymentBrands();
        $idealIssuers = $this->omniKassaClient->getAllIdealIssuers();

        return $this->render('home/manual_checkout.html.twig', [
            'paymentBrands' => $paymentBrands,
            'idealIssuers' => $idealIssuers,
        ]);
    }

    #[Route('/manual-checkout/process', name: 'manual_checkout_process', methods: ['POST'])]
    public function processManualCheckout(Request $request): Response
    {
        $customer = new CustomerInformation(
            $request->request->get('email'),
            $request->request->get('dateOfBirth'),
            $request->request->get('gender'),
            $request->request->get('initials'),
            $request->request->get('phone'),
            $request->request->get('fullName')
        );

        $shipping = new Address(
            $request->request->get('shippingFirstName'),
            $request->request->get('shippingMiddleName'),
            $request->request->get('shippingLastName'),
            $request->request->get('shippingStreet'),
            $request->request->get('shippingPostalCode'),
            $request->request->get('shippingCity'),
            $request->request->get('shippingCountryCode'),
            $request->request->get('shippingHouseNumber')
        );

        $useShippingAsBilling = 'on' === $request->request->get('useShippingAsBilling');

        if ($useShippingAsBilling) {
            $billing = $shipping;
        } else {
            $billing = new Address(
                $request->request->get('billingFirstName'),
                $request->request->get('billingMiddleName'),
                $request->request->get('billingLastName'),
                $request->request->get('billingStreet'),
                $request->request->get('billingPostalCode'),
                $request->request->get('billingCity'),
                $request->request->get('billingCountryCode'),
                $request->request->get('billingHouseNumber')
            );
        }

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

        $paymentBrand = $request->request->get('paymentBrand');

        $paymentBrandForce = null;

        $initiatingParty = $request->request->get('initiatingParty');
        $enableCardOnFile = 'on' === $request->request->get('enableCardOnFile');
        $skipHppResultPage = 'on' === $request->request->get('skipHppResultPage');
        $shopperReference = $request->request->get('shopperReference');
        $shopperBankStatementReference = $request->request->get('shopperBankStatementReference');

        $shippingCostMoney = null;
        if ($shippingCost > 0) {
            $shippingCostMoney = Money::fromCents('EUR', (int) ($shippingCost * 100));
        }

        $idealIssuer = $request->request->get('idealIssuer');
        $brandMetaDataArray = [
            'enableCardOnFile' => $enableCardOnFile,
        ];

        if ('IDEAL' === $paymentBrand && !empty($idealIssuer)) {
            $brandMetaDataArray['issuerId'] = $idealIssuer;
        }

        $brandMetaData = PaymentBrandMetaData::createFrom($brandMetaDataArray);

        $merchantOrder = new MerchantOrder(
            bin2hex(random_bytes(8)),
            'Manual checkout order',
            $items,
            $totalMoney,
            $shipping,
            'nl',
            $this->merchantReturnUrl,
            $paymentBrand,
            $paymentBrandForce,
            $customer,
            $billing,
            $initiatingParty,
            $skipHppResultPage,
            $brandMetaData,
            $enableCardOnFile,
            $shopperReference,
            $shippingCostMoney,
            $shopperBankStatementReference
        );

        try {
            $result = $this->omniKassaClient->announceOrder($merchantOrder);
            $redirectUrl = $result->getRedirectUrl();
            // Store order ID in session for later use
            $request->getSession()->set('omnikassaOrderId', $result->getOmnikassaOrderId());

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
