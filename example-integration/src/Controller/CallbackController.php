<?php

namespace OmniKassa\ExampleIntegration\Controller;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\AnnouncementResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SigningKey;
use OmniKassa\ExampleIntegration\Service\Service\Contract\OmniKassaClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CallbackController extends AbstractController
{
    private OmniKassaClientInterface $omniKassaClient;
    private string $signingKey;

    public function __construct(OmniKassaClientInterface $omniKassaClient, string $signingKey)
    {
        $this->omniKassaClient = $omniKassaClient;
        $this->signingKey = $signingKey;
    }

    #[Route('/callback/order', name: 'callback', methods: ['GET'])]
    public function callback(Request $request): Response
    {
        $orderId = $request->query->get('order_id');
        $status = $request->query->get('status');
        $signature = $request->query->get('signature');

        $sessionOrderId = $request->getSession()->get('omnikassaOrderId');

        $orderDetails = null;
        $announcementResponse = null;
        $error = null;

        if ($sessionOrderId) {
            try {
                $orderDetails = $this->omniKassaClient->getOrderById($sessionOrderId);
            } catch (\Exception $e) {
                // If order details retrieval fails, fall back to announcement
                if ($orderId && $status && $signature) {
                    try {
                        $data = [
                            'order_id' => $orderId,
                            'status' => $status,
                            'signature' => $signature,
                        ];
                        $announcement = new AnnouncementResponse(json_encode($data), new SigningKey($this->signingKey));
                        $announcementResponse = $this->omniKassaClient->retrieveAnnouncement($announcement);
                    } catch (\Exception $e2) {
                        $error = 'Unable to retrieve order information: '.$e2->getMessage();
                    }
                }
            }
        }

        return $this->render('home/callback.html.twig', [
            'orderDetails' => $orderDetails,
            'announcementResponse' => $announcementResponse,
            'error' => $error,
            'orderId' => $orderId,
            'sessionOrderId' => $sessionOrderId,
            'status' => $status,
            'signature' => $signature,
        ]);
    }
}
