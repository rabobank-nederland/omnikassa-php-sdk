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
    public function refund(): Response
    {
        return $this->render('home/refund.html.twig');
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

            $refundJson = json_encode($refundRequest, JSON_PRETTY_PRINT);

            return $this->render('home/refund_result.html.twig', [
                'orderId' => $orderId,
                'refundRequest' => $refundJson,
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
