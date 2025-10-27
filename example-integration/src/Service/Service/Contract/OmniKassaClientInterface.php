<?php

namespace OmniKassa\ExampleIntegration\Service\Service\Contract;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request\MerchantOrder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\AnnouncementResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\MerchantOrderResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\MerchantOrderStatusResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\OrderDetails;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\PaymentBrandInfo;

interface OmniKassaClientInterface
{
    public function announceOrder(MerchantOrder $merchantOrder): MerchantOrderResponse;

    public function retrieveAnnouncement(AnnouncementResponse $announcementResponse): MerchantOrderStatusResponse;

    public function getOrderById(string $orderId): OrderDetails;

    /** @return PaymentBrandInfo[] */
    public function getAllPaymentBrands(): array;

    public function getAllIdealIssuers(): array;

    public function getStoredCards(string $shopperRef): array;

    public function deleteStoredCard(string $shopperRef, string $storedCardRef): void;

    public function getAllCachedOrders(): array;
}
