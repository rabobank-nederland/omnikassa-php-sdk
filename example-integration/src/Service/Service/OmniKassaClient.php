<?php

namespace OmniKassa\ExampleIntegration\Service\Service;

use Carbon\CarbonImmutable;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\endpoint\Endpoint;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request\MerchantOrder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\AnnouncementResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\MerchantOrderResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\MerchantOrderStatusResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\OrderDetails;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SigningKey;
use OmniKassa\ExampleIntegration\Service\Provider\Contract\TokenProviderInterface;
use OmniKassa\ExampleIntegration\Service\Service\Contract\OmniKassaClientInterface;
use Psr\Cache\CacheItemPoolInterface;

class OmniKassaClient implements OmniKassaClientInterface
{
    private const CACHE_KEY_INDEX = 'omnikassa_order_index';
    private const CACHE_KEY_PREFIX = 'omnikassa_order_';
    private Endpoint $endpoint;

    public function __construct(private CacheItemPoolInterface $cacheItemPool, string $baseUrl, TokenProviderInterface $tokenProvider, string $signingKey)
    {
        $this->endpoint = Endpoint::createInstance(
            $baseUrl,
            new SigningKey($signingKey),
            $tokenProvider,
        );
    }

    // Order Announcement
    public function announceOrder(MerchantOrder $merchantOrder): MerchantOrderResponse
    {
        $response = $this->endpoint->announce($merchantOrder);

        $cacheKey = self::CACHE_KEY_PREFIX.CarbonImmutable::now()->format('His');

        $this->saveCacheKey($cacheKey);

        $cacheItem = $this->cacheItemPool->getItem($cacheKey);
        $cacheItem->set([
            'omnikassaOrderId' => $response->getOmnikassaOrderId(),
            'orderTotal' => $merchantOrder->getAmount()->getAmount(),
            'timestamp' => time(),
        ]);
        $cacheItem->expiresAfter(3600);
        $this->cacheItemPool->save($cacheItem);

        return $response;
    }

    // Order Status
    public function retrieveAnnouncement(AnnouncementResponse $announcementResponse): MerchantOrderStatusResponse
    {
        return $this->endpoint->retrieveAnnouncement($announcementResponse);
    }

    public function getOrderById(string $orderId): OrderDetails
    {
        return $this->endpoint->getOrderById($orderId);
    }

    // Payment Brands
    public function getAllPaymentBrands(): array
    {
        $response = $this->endpoint->retrievePaymentBrands();

        return $response->getPaymentBrands();
    }

    // iDEAL Issuers
    public function getAllIdealIssuers(): array
    {
        $response = $this->endpoint->retrieveIDEALIssuers();

        return $response->getIssuers();
    }

    // Stored Cards
    public function getStoredCards(string $shopperRef): array
    {
        return $this->endpoint->getStoredCards($shopperRef);
    }

    public function deleteStoredCard(string $shopperRef, string $storedCardRef): void
    {
        $this->endpoint->deleteStoredCard($shopperRef, $storedCardRef);
    }

    public function getAllCachedOrders(): array
    {
        $item = $this->cacheItemPool->getItem(self::CACHE_KEY_INDEX);

        if (false === $item->isHit()) {
            return [];
        }

        $index = $item->get();

        $items = [];
        foreach ($index as $cacheKey) {
            $item = $this->cacheItemPool->getItem($cacheKey);

            if ($item->isHit()) {
                $items[$cacheKey] = $item->get();
            } else {
                $this->removeCacheKey($cacheKey);
            }
        }

        return $items;
    }

    private function saveCacheKey(string $cacheKey): void
    {
        $indexCacheItem = $this->cacheItemPool->getItem(self::CACHE_KEY_INDEX);
        $index = $indexCacheItem->isHit() ? $indexCacheItem->get() : [];

        if (false === in_array($cacheKey, $index, true)) {
            $index[] = $cacheKey;
            $indexCacheItem->set($index);
            $this->cacheItemPool->save($indexCacheItem);
        }
    }

    private function removeCacheKey(string $cacheKey): void
    {
        $indexCacheItem = $this->cacheItemPool->getItem(self::CACHE_KEY_INDEX);
        $index = $indexCacheItem->isHit() ? $indexCacheItem->get() : [];

        $keyPosition = array_search($cacheKey, $index, true);
        if (false !== $keyPosition) {
            unset($index[$keyPosition]);
            $indexCacheItem->set(array_values($index));
            $this->cacheItemPool->save($indexCacheItem);
        }
    }
}
