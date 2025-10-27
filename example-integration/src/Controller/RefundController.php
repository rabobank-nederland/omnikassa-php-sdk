<?php

namespace OmniKassa\ExampleIntegration\Controller;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request\InitiateRefundRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RefundController extends AbstractController
{
    #[Route('/refund', name: 'refund')]
    public function refund(Request $request): Response
    {
        $orderId = $request->query->get('orderId');

        return $this->render('home/refund.html.twig', [
            'orderId' => $orderId,
        ]);
    }

    #[Route('/refund/process', name: 'refund_process', methods: ['POST'])]
    public function processRefund(Request $request): Response
    {
        $orderId = $request->request->get('orderId');
        $amount = $request->request->get('amount');
        $currency = $request->request->get('currency', 'EUR');
        $description = $request->request->get('description');
        $vatCategory = $request->request->get('vatCategory');

        try {
            $money = Money::fromCents($currency, (int) $amount);
            $refundRequest = new InitiateRefundRequest($money, $description, $vatCategory);

            // For demo purposes, we'll simulate the API call
            // In a real implementation, you would send this to the OmniKassa API
            $refundResult = [
                'refundId' => 'REFUND-'.substr($orderId, -8),
                'status' => 'COMPLETED',
                'amount' => $amount,
                'currency' => $currency,
                'description' => $description ?: 'Demo refund for testing purposes',
                'createdAt' => date('Y-m-d H:i:s'),
            ];

            return $this->render('home/refund_result.html.twig', [
                'orderId' => $orderId,
                'refundResult' => $refundResult,
            ]);
        } catch (\Exception $e) {
            return $this->render('home/refund.html.twig', [
                'error' => $e->getMessage(),
                'orderId' => $orderId,
                'amount' => $amount,
                'currency' => $currency,
                'description' => $description,
                'vatCategory' => $vatCategory,
            ]);
        }
    }
}
