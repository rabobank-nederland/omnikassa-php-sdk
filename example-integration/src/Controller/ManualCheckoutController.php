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
        return $this->renderManualCheckoutForm();
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
        $subtotalCents = 0;
        $itemsData = $request->request->all('items');
        if ($itemsData) {
            foreach ($itemsData as $itemData) {
                if (!is_numeric($itemData['quantity']) || (int) $itemData['quantity'] <= 0) {
                    return $this->renderManualCheckoutForm([
                        'error' => 'Quantity must be a positive number.',
                    ] + $request->request->all());
                }
                if (!is_numeric($itemData['unitPrice']) || (float) $itemData['unitPrice'] < 0) {
                    return $this->renderManualCheckoutForm([
                        'error' => 'Unit price must be zero or greater.',
                    ] + $request->request->all());
                }

                $quantity = (int) $itemData['quantity'];
                $unitPrice = (float) $itemData['unitPrice'];
                $unitPriceCents = (int) round($unitPrice * 100);
                $totalPriceCents = $quantity * $unitPriceCents;
                $subtotalCents += $totalPriceCents;

                $items[] = new OrderItem(
                    $itemData['name'],
                    $itemData['description'] ?? '',
                    $quantity,
                    Money::fromCents('EUR', $unitPriceCents),
                    Money::fromCents('EUR', $totalPriceCents),
                    $itemData['taxCategory']
                );
            }
        }
        if (empty($items)) {
            return $this->renderManualCheckoutForm([
                'error' => 'At least one item is required.',
            ] + $request->request->all());
        }

        $shippingCostRaw = $request->request->get('shippingCost', 0);
        if (!is_numeric($shippingCostRaw) || (float) $shippingCostRaw < 0) {
            return $this->renderManualCheckoutForm([
                'error' => 'Shipping cost must be zero or greater.',
            ] + $request->request->all());
        }

        $shippingCost = (float) $shippingCostRaw;
        $shippingCostCents = (int) round($shippingCost * 100);
        $totalCents = $subtotalCents + $shippingCostCents;
        $totalMoney = Money::fromCents('EUR', $totalCents);

        $paymentBrand = $request->request->get('paymentBrand');

        $paymentBrandForce = $request->request->get('paymentBrandForce');
        if (!$paymentBrand) {
            // No payment brand chosen: ignore any force value to prevent API errors
            $paymentBrandForce = null;
        } elseif (empty($paymentBrandForce)) {
            $paymentBrands = $this->omniKassaClient->getAllPaymentBrands();
            $idealIssuers = $this->omniKassaClient->getAllIdealIssuers();

            return $this->render('home/manual_checkout.html.twig', [
                'error' => 'Please choose a payment brand force when a payment brand is selected.',
                'paymentBrands' => $paymentBrands,
                'idealIssuers' => $idealIssuers,
            ] + $request->request->all());
        }

        $initiatingParty = $request->request->get('initiatingParty');
        $enableCardOnFile = 'on' === $request->request->get('enableCardOnFile');
        $skipHppResultPage = 'on' === $request->request->get('skipHppResultPage');
        $shopperReference = $request->request->get('shopperReference');
        $shopperBankStatementReference = $request->request->get('shopperBankStatementReference');

        $shippingCostMoney = $shippingCostCents > 0 ? Money::fromCents('EUR', $shippingCostCents) : null;

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
            // Load payment brands and iDEAL issuers for form redisplay
            $paymentBrands = $this->omniKassaClient->getAllPaymentBrands();
            $idealIssuers = $this->omniKassaClient->getAllIdealIssuers();

            return $this->render('home/manual_checkout.html.twig', [
                'error' => 'Unable to process manual checkout: '.$e->getMessage(),
                'paymentBrands' => $paymentBrands,
                'idealIssuers' => $idealIssuers,
                // Preserve form data for user corrections
            ] + $request->request->all());
        }
    }

    private function renderManualCheckoutForm(array $data = []): Response
    {
        $data['paymentBrands'] = $data['paymentBrands'] ?? $this->omniKassaClient->getAllPaymentBrands();
        $data['idealIssuers'] = $data['idealIssuers'] ?? $this->omniKassaClient->getAllIdealIssuers();

        return $this->render('home/manual_checkout.html.twig', $data);
    }
}
