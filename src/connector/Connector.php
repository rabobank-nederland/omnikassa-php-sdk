<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\connector;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request\MerchantOrderRequest;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\AnnouncementResponse;

/**
 * This interface describes the abstract calls you can make to the Rabobank OmniKassa.
 */
interface Connector
{
    /**
     * Announce an order.
     *
     * @return string json response body
     */
    public function announceMerchantOrder(MerchantOrderRequest $order);

    /**
     * Retrieve the order details from an announcement.
     *
     * @return string json response body
     */
    public function getAnnouncementData(AnnouncementResponse $announcement);

    /**
     * Retrieve the payment brands with their corresponding status.
     *
     * @return string json response body
     */
    public function getPaymentBrands();

    /**
     * Retrieve the iDEAL issuers.
     */
    public function getIDEALIssuers(): string;
}
