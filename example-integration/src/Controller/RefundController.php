<?php

namespace OmniKassa\ExampleIntegration\Controller;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request\InitiateRefundRequest;
use OmniKassa\ExampleIntegration\Service\Service\Contract\OmniKassaClientInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RefundController extends AbstractController
{
    public function __construct(OmniKassaClientInterface $omniKassaClient)
    {
        $this->omniKassaClient = $omniKassaClient;
    }

    #[Route('/refund', name: 'refund')]
    public function refund(Request $request): Response
    {
        $transactionId = $request->query->get('transactionId') ?? $request->query->get('orderId');

        return $this->render('home/refund.html.twig', [
            'transactionId' => $transactionId,
            'amount' => '',
            'currency' => 'EUR',
            'description' => '',
            'vatCategory' => 'HIGH',
        ]);
    }

    #[Route('/refund/process', name: 'refund_process', methods: ['POST'])]
    public function processRefund(Request $request): Response
    {
        $transactionId = $request->request->get('transactionId');
        $amount = $request->request->get('amount');
        $currency = $request->request->get('currency', 'EUR');
        $description = $request->request->get('description');
        $vatCategory = $request->request->get('vatCategory') ?: 'HIGH';

        if (empty($transactionId)) {
            return $this->render('home/refund.html.twig', [
                'error' => 'Transaction ID is required.',
            ] + $request->request->all());
        }

        if (!is_numeric($amount) || (int) $amount <= 0) {
            return $this->render('home/refund.html.twig', [
                'error' => 'Amount must be a positive number of cents.',
            ] + $request->request->all());
        }

        try {
            $money = Money::fromCents($currency, (int) $amount);
            $refundRequest = new InitiateRefundRequest($money, $description, $vatCategory);

            $requestId = Uuid::uuid4();
            $refundResponse = $this->omniKassaClient->initiateRefundTransaction($refundRequest, $transactionId, $requestId);

            $refundResult = [
                'refundId' => $refundResponse->getRefundId(),
                'status' => $refundResponse->getStatus(),
                'amount' => $refundResponse->getRefundMoney()->getAmount(),
                'currency' => $refundResponse->getRefundMoney()->getCurrency(),
                'description' => $refundResponse->getDescription(),
                'createdAt' => $refundResponse->getCreatedAt()->format('c'),
            ];

            return $this->render('home/refund_result.html.twig', [
                'transactionId' => $transactionId,
                'refundResult' => $refundResult,
            ]);
        } catch (\Exception $e) {
            return $this->render('home/refund.html.twig', [
                'error' => $e->getMessage(),
                'transactionId' => $transactionId,
                'amount' => $amount,
                'currency' => $currency,
                'description' => $description,
                'vatCategory' => $vatCategory,
            ]);
        }
    }

    #[Route('/refund/fetch/{transactionId}/{refundId}', name: 'fetch_refund_transaction', methods: ['GET'])]
    public function fetchRefundDetails(string $transactionId, string $refundId): Response
    {
        try {
            $refundDetails = $this->omniKassaClient->fetchRefundTransactionDetails($transactionId, $refundId);

            $refundData = [
                'refundId' => $refundDetails->getRefundId(),
                'refundTransactionId' => $refundDetails->getRefundTransactionId(),
                'createdAt' => $refundDetails->getCreatedAt()->format('c'),
                'updatedAt' => $refundDetails->getUpdatedAt() ? $refundDetails->getUpdatedAt()->format('c') : null,
                'amount' => $refundDetails->getRefundMoney()->getAmount(),
                'currency' => $refundDetails->getRefundMoney()->getCurrency(),
                'vatCategory' => $refundDetails->getVatCategory(),
                'paymentBrand' => $refundDetails->getPaymentBrand() ? strtoupper($refundDetails->getPaymentBrand()) : null,
                'status' => $refundDetails->getStatus(),
                'description' => $refundDetails->getDescription(),
                'transactionId' => $refundDetails->getTransactionId(),
            ];

            return $this->render('home/fetch_refund_details.html.twig', [
                'refundData' => $refundData,
            ]);
        } catch (\Exception $e) {
            return $this->render('home/fetch_refund_details.html.twig', [
                'error' => $e->getMessage(),
                'transactionId' => $transactionId,
            ]);
        }
    }

    #[Route('/refund/fetch-refundable/{transactionId}', name: 'fetch_refundable_transaction_details')]
    public function fetchRefundableTransactionDetails(string $transactionId): Response
    {
        try {
            $refundableDetails = $this->omniKassaClient->fetchRefundableTransactionDetails($transactionId);

            $refundableData = [
                'transactionId' => $refundableDetails->getTransactionId(),
                'refundableAmount' => $refundableDetails->getRefundableMoney()->getAmount(),
                'refundableCurrency' => $refundableDetails->getRefundableMoney()->getCurrency(),
                'expiryDatetime' => $refundableDetails->getExpiryDatetime()->format('c'),
            ];

            return $this->render('home/fetch_refundable_details.html.twig', [
                'refundableData' => $refundableData,
            ]);
        } catch (\Exception $e) {
            return $this->render('home/fetch_refundable_details.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
