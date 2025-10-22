<?php

namespace OmniKassa\ExampleIntegration\Controller;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\AnnouncementResponse;
use OmniKassa\ExampleIntegration\Service\Service\Contract\OmniKassaClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderStatusController extends AbstractController
{
    private OmniKassaClientInterface $omniKassaClient;

    public function __construct(OmniKassaClientInterface $omniKassaClient)
    {
        $this->omniKassaClient = $omniKassaClient;
    }

    #[Route('/order-status', name: 'order_status', methods: ['GET', 'POST'])]
    public function orderStatus(Request $request): Response
    {
        $orderDetails = null;
        $error = null;
        $orderId = null;
        $cachedOrders = [];

        if ($request->isMethod('POST')) {
            $orderId = $request->request->get('orderId');
        } elseif ($request->isMethod('GET')) {
            $orderId = $request->query->get('orderId');
        }

        if (!empty($orderId)) {
            try {
                $orderDetails = $this->omniKassaClient->getOrderById($orderId);
            } catch (\Exception $e) {
                $error = 'Unable to retrieve order status: '.$e->getMessage();
            }
        } elseif ($request->isMethod('GET') && $request->query->has('orderId')) {
            $error = 'Please provide a valid order ID';
        }

        // Get all cached orders
        try {
            $cachedOrders = $this->omniKassaClient->getAllCachedOrders();
            // Sort by timestamp descending (most recent first)
            usort($cachedOrders, function ($a, $b) {
                return $b['timestamp'] <=> $a['timestamp'];
            });
        } catch (\Exception $e) {
            // Ignore cache errors for now
        }

        return $this->render('home/order_status.html.twig', [
            'orderDetails' => $orderDetails,
            'error' => $error,
            'orderId' => $orderId,
            'cachedOrders' => $cachedOrders,
        ]);
    }

    #[Route('/retrieve-announcement', name: 'retrieve_announcement', methods: ['GET', 'POST'])]
    public function retrieveAnnouncement(Request $request): Response
    {
        $announcementResponse = null;
        $error = null;

        if ($request->isMethod('POST')) {
            $announcementData = $request->request->get('announcementData');
            if (!empty($announcementData)) {
                try {
                    $data = json_decode($announcementData, true, JSON_THROW_ON_ERROR);
                    $announcement = new AnnouncementResponse($data);
                    $announcementResponse = $this->omniKassaClient->retrieveAnnouncement($announcement);
                } catch (\Exception $e) {
                    $error = 'Unable to retrieve announcement: '.$e->getMessage();
                }
            } else {
                $error = 'Please provide announcement data';
            }
        }

        return $this->render('home/order_status.html.twig', [
            'announcementResponse' => $announcementResponse,
            'error' => $error,
        ]);
    }
}
