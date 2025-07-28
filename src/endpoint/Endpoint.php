<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\endpoint;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\ApiConnector;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\Connector;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\TokenProvider;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request\InitiateRefundRequest;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request\MerchantOrder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request\MerchantOrderRequest;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\AnnouncementResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\IdealIssuersResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\MerchantOrderResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\MerchantOrderStatusResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\PaymentBrandsResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\RefundDetailsResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\TransactionRefundableDetailsResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\InvalidSignatureException;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SigningKey;

/**
 * This class exposes the functionality available in the SDK.
 *
 * @api
 */
class Endpoint
{
    /** @var Connector */
    private $connector;
    /** @var SigningKey */
    private $signingKey;

    /**
     * @internal
     */
    protected function __construct(Connector $connector, SigningKey $signingKey)
    {
        $this->signingKey = $signingKey;
        $this->connector = $connector;
    }

    /**
     * Create an instance of the endpoint to connect to the Rabo Smart Pay.
     *
     * @param string        $baseURL          the base URL pointing towards the Rabo Smart Pay environment
     * @param SigningKey    $signingKey       the secret key used to sign messages
     * @param TokenProvider $tokenProvider    used to store and retrieve token related information to/from
     * @param ?string       $userAgent        an optional user agent value
     * @param ?string       $partnerReference an optional partner reference
     *
     * @return Endpoint
     */
    public static function createInstance($baseURL, SigningKey $signingKey, TokenProvider $tokenProvider, $userAgent = null, $partnerReference = null)
    {
        return new Endpoint(ApiConnector::withGuzzle($baseURL, $tokenProvider, $userAgent, $partnerReference), $signingKey);
    }

    /**
     * Announce an order.
     *
     * @return string an URL the customer shall be redirected to
     *
     * @throws \JsonMapper_Exception
     *
     * @deprecated use announce($merchantOrder) instead
     */
    public function announceMerchantOrder(MerchantOrder $merchantOrder)
    {
        return $this->announce($merchantOrder)->getRedirectUrl();
    }

    /**
     * Announce an order.
     *
     * @param MerchantOrder $merchantOrder the order to announce
     *
     * @return MerchantOrderResponse response object containing the URL the customer shall be redirected to
     *                               as well as a unique ID that Rabo Omnikassa assigned to the order
     *
     * @throws \JsonMapper_Exception
     */
    public function announce(MerchantOrder $merchantOrder)
    {
        $request = new MerchantOrderRequest($merchantOrder);

        $responseAsJson = $this->connector->announceMerchantOrder($request);

        return new MerchantOrderResponse($responseAsJson);
    }

    /**
     * Retrieve the merchant order status from the given announcement.
     *
     * @return MerchantOrderStatusResponse
     *
     * @throws \JsonMapper_Exception
     * @throws InvalidSignatureException
     */
    public function retrieveAnnouncement(AnnouncementResponse $announcementResponse)
    {
        $announcementDataAsJson = $this->connector->getAnnouncementData($announcementResponse);

        // When we get different types of announcements, make the switch to handle response message differently.

        return new MerchantOrderStatusResponse($announcementDataAsJson, $this->signingKey);
    }

        /**
         * This function will initiate refund transaction.
         *
         * @param InitiateRefundRequest $refundRequest The request for refund
         * @param string                $transactionId The UUID of transaction for which the refund request is sent
         * @param string                $requestId     The unique request ID (UUID) of this refund, for your own internal reference
         *
         * @return RefundDetailsResponse the response contains refund details, which can be used to update the refund with the latest status
         */
        public function initiateRefundTransaction(InitiateRefundRequest $refundRequest, string $transactionId, string $requestId): RefundDetailsResponse
        {
            $refundDetailsDataAsJson = $this->connector->postRefundRequest($refundRequest, $transactionId, $requestId);

            return new RefundDetailsResponse($refundDetailsDataAsJson);
        }

        /**
         * This function retrieves refund details.
         *
         * @param string $transactionId The UUID of transaction for which the refund request was sent
         * @param string $refundId      The UUID of this refund
         *
         * @return RefundDetailsResponse the response contains refund details
         */
        public function fetchRefundTransaction(string $transactionId, string $refundId): RefundDetailsResponse
        {
            $refundDetailsDataAsJson = $this->connector->getRefundRequest($transactionId, $refundId);

            return new RefundDetailsResponse($refundDetailsDataAsJson);
        }

        /**
         * This function will get details for specific refund within transaction.
         *
         * @param string $transactionId The UUID of transaction for which the refund request is sent
         *
         * @return TransactionRefundableDetailsResponse the response contains refund options, which can be used to create a refund with initiateRefundTransaction()
         */
        public function fetchRefundableTransactionDetails(string $transactionId): TransactionRefundableDetailsResponse
        {
            $refundableDetailsDataAsJson = $this->connector->getRefundableDetails($transactionId);

            return new TransactionRefundableDetailsResponse($refundableDetailsDataAsJson);
        }

    /**
     * Retrieve the payment brands name and status.
     *
     * @return PaymentBrandsResponse
     *
     * @throws \JsonMapper_Exception
     */
    public function retrievePaymentBrands()
    {
        $responseAsJson = $this->connector->getPaymentBrands();

        return new PaymentBrandsResponse($responseAsJson);
    }

    /**
     * Retrieve the iDEAL issuers.
     */
    public function retrieveIDEALIssuers(): IdealIssuersResponse
    {
        $responseAsJson = $this->connector->getIDEALIssuers();

        return new IdealIssuersResponse($responseAsJson);
    }
}
