<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\connector;

use DateTime;
use DateTimeZone;
use Exception;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\http\GuzzleRESTTemplate;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\http\RESTTemplate;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\AccessToken;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request\MerchantOrderRequest;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\AnnouncementResponse;

/**
 * The Connector implementation. It is responsible for separating the rest interface from the endpoint of the SDK.
 */
class ApiConnector implements Connector
{
    public const VERSION = '1.17.0';
    public const SMARTPAY_USER_AGENT = 'RabobankOmnikassaPHPSDK/'.self::VERSION;

    /** @var RESTTemplate */
    private $restTemplate;
    /** @var TokenProvider */
    private $tokenProvider;
    /** @var AccessToken */
    private $accessToken;
    /** @var string */
    private $partnerReference;
    /** @var string */
    private $userAgent;

    /**
     * @internal
     */
    protected function __construct(RESTTemplate $restTemplate, TokenProvider $tokenProvider)
    {
        $this->restTemplate = $restTemplate;
        $this->tokenProvider = $tokenProvider;
    }

    /**
     * Construct a Guzzle based ApiConnector.
     *
     * @param string  $baseURL
     * @param ?string $userAgent
     * @param ?string $partnerReference
     *
     * @return ApiConnector
     */
    public static function withGuzzle($baseURL, TokenProvider $tokenProvider, $userAgent, $partnerReference)
    {
        $curlTemplate = new GuzzleRESTTemplate($baseURL);

        $apiConnector = new ApiConnector($curlTemplate, $tokenProvider);
        $apiConnector->setUserAgent($userAgent);
        $apiConnector->setPartnerReference($partnerReference);

        return $apiConnector;
    }

    /**
     * Announce an order.
     *
     * @return string json response body
     *
     * @deprecated use the new announce order method @see ApiConnector::announce()
     */
    public function announceMerchantOrder(MerchantOrderRequest $order)
    {
        return $this->announce($order);
    }

    /**
     * Announce an order.
     *
     * @return string json response body
     */
    public function announce(MerchantOrderRequest $order)
    {
        return $this->performAction(function () use (&$order) {
            $this->restTemplate->setToken($this->accessToken->getToken());
            $this->restTemplate->setUserAgent($this->getUserAgentString());

            return $this->restTemplate->post('order/server/api/v2/order', $order);
        });
    }

    /**
     * Retrieve the order details from an announcement.
     *
     * @return string json response body
     */
    public function getAnnouncementData(AnnouncementResponse $announcement)
    {
        return $this->performAction(function () use (&$announcement) {
            $this->restTemplate->setToken($announcement->getAuthentication());

            return $this->restTemplate->get('order/server/api/events/results/'.$announcement->getEventName());
        });
    }

    /**
     * Retrieve the payment brands with their corresponding status.
     *
     * @return string json response body
     */
    public function getPaymentBrands()
    {
        return $this->performAction(function () {
            $this->restTemplate->setToken($this->accessToken->getToken());

            return $this->restTemplate->get('order/server/api/payment-brands');
        });
    }

    /**
     * Retrieve the iDEAL issuers.
     *
     * @return string json response body
     */
    public function getIDEALIssuers(): string
    {
        return $this->performAction(function () {
            $this->restTemplate->setToken($this->accessToken->getToken());

            return $this->restTemplate->get('ideal/server/api/v2/issuers');
        });
    }

    /**
     * Retrieve order details by orderId (v2/orders/{orderId}).
     *
     * @param non-empty-string $orderId
     *
     * @return string json response body
     */
    public function getOrderById($orderId): string
    {
        return $this->performAction(function () use ($orderId) {
            $this->restTemplate->setToken($this->accessToken->getToken());

            return $this->restTemplate->get('/v2/orders/'.$orderId);
        });
    }

    public function getStoredCards(string $shopperRef): string
    {
        return $this->performAction(function () use ($shopperRef) {
            $this->restTemplate->setToken($this->accessToken->getToken());

            return $this->restTemplate->get('/v1/shopper-payment-details', [
                'shopper-ref' => $shopperRef,
            ]);
        });
    }

    public function deleteStoredCard(string $shopperRef, string $storedCardRef): void
    {
        $this->performAction(function () use ($shopperRef, $storedCardRef) {
            $this->restTemplate->setToken($this->accessToken->getToken());

            return $this->restTemplate->delete(sprintf('v1/shopper-payment-details/%s', $storedCardRef), [
                'shopper-ref' => $shopperRef,
            ]);
        });
    }

    /**
     * Perform a Rabo Smart Pay related rest action.
     * This first checks the access token and retrieves one if it is invalid, expired or non existing.
     * Then it executes the action.
     *
     * @param callable $action
     *
     * @return mixed result of the action
     */
    private function performAction($action)
    {
        $this->validateToken();

        return $action();
    }

    private function validateToken()
    {
        try {
            if (null === $this->accessToken) {
                $this->accessToken = $this->tokenProvider->getAccessToken();
            }

            if (null === $this->accessToken || $this->isExpired($this->accessToken)) {
                $this->updateToken();
            }
        } catch (Exception $invalidAccessTokenException) {
            $this->updateToken();
        }
    }

    /**
     * @return bool
     */
    private function isExpired(AccessToken $token)
    {
        $validUntil = $token->getValidUntil();
        $currentDate = new DateTime('now', new DateTimeZone('UTC'));
        // Difference in seconds
        $difference = $validUntil->getTimestamp() - $currentDate->getTimestamp();

        return ($difference / $token->getDurationInSeconds()) < 0.05;
    }

    private function updateToken()
    {
        $this->accessToken = $this->retrieveNewToken();
        $this->tokenProvider->setAccessToken($this->accessToken);
    }

    /**
     * @return AccessToken
     */
    private function retrieveNewToken()
    {
        $refreshToken = $this->tokenProvider->getRefreshToken();

        $this->restTemplate->setToken($refreshToken);
        $accessTokenJson = $this->restTemplate->get('gatekeeper/refresh');

        return AccessToken::fromJson($accessTokenJson);
    }

    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }

    public function setPartnerReference($partnerReference)
    {
        $this->partnerReference = $partnerReference;
    }

    /**
     * @return ?string
     */
    public function getPartnerReference()
    {
        return $this->partnerReference;
    }

    /**
     * @return ?string
     */
    private function getUserAgentString()
    {
        $userAgentHeader = self::SMARTPAY_USER_AGENT;
        if (!empty($this->userAgent)) {
            $userAgentHeader .= ' '.$this->userAgent;
        }
        if (!empty($this->partnerReference)) {
            $userAgentHeader .= ' (pr: '.$this->partnerReference.')';
        }

        return $userAgentHeader;
    }
}
