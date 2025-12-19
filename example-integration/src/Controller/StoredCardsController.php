<?php

namespace OmniKassa\ExampleIntegration\Controller;

use OmniKassa\ExampleIntegration\Service\Service\Contract\OmniKassaClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StoredCardsController extends AbstractController
{
    private const DELETE_SUCCESS_MESSAGE = 'Stored card deleted successfully';
    private const FETCH_ERROR_MESSAGE = 'Unable to retrieve stored cards';
    private const DELETE_ERROR_MESSAGE = 'Unable to delete stored card';
    private const MISSING_REFERENCE_MESSAGE = 'Please provide a shopper reference';

    private OmniKassaClientInterface $omniKassaClient;
    private bool $getStoredCardsAvailable;
    private bool $deleteStoredCardAvailable;

    public function __construct(OmniKassaClientInterface $omniKassaClient)
    {
        $this->omniKassaClient = $omniKassaClient;
        $this->getStoredCardsAvailable = method_exists($omniKassaClient, 'getStoredCards');
        $this->deleteStoredCardAvailable = method_exists($omniKassaClient, 'deleteStoredCard');
    }

    #[Route('/stored-cards', name: 'stored_cards', methods: ['GET', 'POST'])]
    public function storedCards(Request $request): Response
    {
        $data = [
            'error' => null,
            'success' => null,
            'cards' => null,
            'shopperRef' => $request->get('shopperRef') ?: $request->query->get('shopperRef'),
        ];

        if ($request->isMethod('POST')) {
            $data = $this->handlePostRequest($request, $data);
        } else {
            $this->handleGetRequest($request, $data);
        }

        return $this->render('home/stored_cards.html.twig', array_merge($data, [
            'getStoredCardsAvailable' => $this->getStoredCardsAvailable,
            'deleteStoredCardAvailable' => $this->deleteStoredCardAvailable,
        ]));
    }

    private function handlePostRequest(Request $request, array $data): array
    {
        $action = $request->request->get('action');
        $shopperRef = $request->request->get('shopperRef');
        $storedCardRef = $request->request->get('storedCardRef');

        if (!$shopperRef) {
            $data['error'] = self::MISSING_REFERENCE_MESSAGE;

            return $data;
        }

        if ('delete' === $action && $storedCardRef) {
            return $this->handleDeleteAction($shopperRef, $storedCardRef, $data);
        }

        return $this->handleFetchAction($shopperRef, $data);
    }

    private function handleDeleteAction(string $shopperRef, string $storedCardRef, array $data): array
    {
        try {
            $this->omniKassaClient->deleteStoredCard($shopperRef, $storedCardRef);
            $data['success'] = self::DELETE_SUCCESS_MESSAGE;
        } catch (\Exception $e) {
            $data['error'] = sprintf('%s: %s', self::DELETE_ERROR_MESSAGE, $e->getMessage());
        }

        // Always try to refetch cards after delete attempt
        try {
            $data['cards'] = $this->omniKassaClient->getStoredCards($shopperRef);
        } catch (\Exception $e) {
            // Ignore fetch errors when deleting
        }

        return $data;
    }

    private function handleFetchAction(string $shopperRef, array $data): array
    {
        try {
            $data['cards'] = $this->omniKassaClient->getStoredCards($shopperRef);
        } catch (\Exception $e) {
            $data['error'] = sprintf('%s: %s', self::FETCH_ERROR_MESSAGE, $e->getMessage());
        }

        return $data;
    }

    private function handleGetRequest(Request $request, array &$data): void
    {
        $shopperRef = $request->query->get('shopperRef');

        if ($shopperRef) {
            $this->handleFetchAction($shopperRef, $data);
        }
    }
}
