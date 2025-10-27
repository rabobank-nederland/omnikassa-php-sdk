<?php

namespace OmniKassa\ExampleIntegration\Controller;

use OmniKassa\ExampleIntegration\Service\Service\Contract\OmniKassaClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StoredCardsController extends AbstractController
{
    private OmniKassaClientInterface $omniKassaClient;
    private bool $getStoredCardsAvailable;
    private bool $deleteStoredCardAvailable;

    public function __construct(OmniKassaClientInterface $omniKassaClient)
    {
        $this->omniKassaClient = $omniKassaClient;
        $this->getStoredCardsAvailable = true; // Assume available since it's in the interface
        $this->deleteStoredCardAvailable = true; // Assume available since it's in the interface
    }

    #[Route('/stored-cards', name: 'stored_cards', methods: ['GET', 'POST'])]
    public function storedCards(Request $request): Response
    {
        $cards = null;
        $error = null;
        $success = null;
        $shopperRef = null;

        if ($request->isMethod('POST')) {
            $shopperRef = $request->request->get('shopperRef');
            $storedCardRef = $request->request->get('storedCardRef');
            $action = $request->request->get('action');

            if ('delete' === $action && !empty($shopperRef) && !empty($storedCardRef)) {
                try {
                    $this->omniKassaClient->deleteStoredCard($shopperRef, $storedCardRef);
                    $success = 'Stored card deleted successfully';
                    // Re-fetch cards after deletion
                    $cards = $this->omniKassaClient->getStoredCards($shopperRef);
                } catch (\Exception $e) {
                    $error = 'Unable to delete stored card: '.$e->getMessage();
                    // Still fetch cards on error
                    try {
                        $cards = $this->omniKassaClient->getStoredCards($shopperRef);
                    } catch (\Exception $e2) {
                        // Ignore
                    }
                }
            } elseif (!empty($shopperRef)) {
                try {
                    $cards = $this->omniKassaClient->getStoredCards($shopperRef);
                } catch (\Exception $e) {
                    $error = 'Unable to retrieve stored cards: '.$e->getMessage();
                }
            } else {
                $error = 'Please provide a shopper reference';
            }
        } elseif ($request->query->has('shopperRef')) {
            // Handle GET request with shopperRef query parameter
            $shopperRef = $request->query->get('shopperRef');
            try {
                $cards = $this->omniKassaClient->getStoredCards($shopperRef);
            } catch (\Exception $e) {
                $error = 'Unable to retrieve stored cards: '.$e->getMessage();
            }
        }

        return $this->render('home/stored_cards.html.twig', [
            'cards' => $cards,
            'error' => $error,
            'success' => $success,
            'shopperRef' => $shopperRef,
            'getStoredCardsAvailable' => $this->getStoredCardsAvailable,
            'deleteStoredCardAvailable' => $this->deleteStoredCardAvailable,
        ]);
    }
}
