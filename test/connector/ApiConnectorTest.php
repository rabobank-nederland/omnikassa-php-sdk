<?php

/**
 * This file is Copyright (c) 2018 Move4Mobile B.V. (https://move4mobile.com).
 */

namespace nl\rabobank\gict\payments_savings\test\omnikassa_sdk\connector;

use DateTime;
use DateTimeZone;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\http\RESTTemplate;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\TokenProvider;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\AccessToken;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SigningKey;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\connector\ApiConnectorWrapper;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\request\MerchantOrderRequestBuilder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\AnnouncementResponseBuilder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\MerchantOrderResponseBuilder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\MerchantOrderStatusResponseBuilder;
use Phake;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ApiConnectorTest extends TestCase
{
    /** @var AccessToken */
    private $accessToken;
    /** @var AccessToken */
    private $expiredAccessToken;
    /** @var AccessToken */
    private $secondAccessToken;
    /** @var string */
    private $refreshToken;
    /** @var ApiConnector */
    private $connector;
    /** @var RESTTemplate */
    private $restTemplate;
    /** @var TokenProvider */
    private $tokenProvider;

    private $signingKey;

    protected function setUp(): void
    {
        $this->signingKey = new SigningKey('secret');

        $this->restTemplate = Phake::mock(RESTTemplate::class);
        $this->tokenProvider = Phake::mock(TokenProvider::class);
        $this->connector = new ApiConnectorWrapper($this->restTemplate, $this->tokenProvider);

        $utc = new DateTimeZone('UTC');
        $this->accessToken = new AccessToken('accessToken1', new DateTime('+1 day', $utc), 1000);
        $this->expiredAccessToken = new AccessToken('expiredAccessToken', new DateTime('-1 day', $utc), 1000);
        $this->secondAccessToken = new AccessToken('accessToken2', new DateTime('+30 day', $utc), 1000);
        $this->refreshToken = 'refreshToken';
    }

    public function testAnnounceOrder()
    {
        $order = MerchantOrderRequestBuilder::makeCompleteRequest();
        $expectedResponse = MerchantOrderResponseBuilder::newInstanceAsJson();

        $this->prepareTokenProviderWithAccessToken($this->accessToken);
        Phake::when($this->restTemplate)->post('order/server/api/v2/order', $order)->thenReturn($expectedResponse);

        $actualResponse = $this->connector->announceMerchantOrder($order);

        Phake::verify($this->restTemplate)->setToken($this->accessToken->getToken());
        Phake::verify($this->restTemplate)->post('order/server/api/v2/order', $order);

        $this->assertEquals($expectedResponse, $actualResponse);
    }

    public function testGetAnnouncementData()
    {
        $announcement = AnnouncementResponseBuilder::newInstance();
        $expectedResponse = $this->makeAnnouncementResponse($announcement->getEventName());

        $this->prepareTokenProviderWithAccessToken($this->accessToken);
        Phake::when($this->restTemplate)->get('order/server/api/events/results/'.$announcement->getEventName())->thenReturn($expectedResponse);

        $actualResponse = $this->connector->getAnnouncementData($announcement);

        Phake::verify($this->restTemplate)->setToken('MyJwt');
        Phake::verify($this->restTemplate)->get('order/server/api/events/results/'.$announcement->getEventName());

        $this->assertEquals($expectedResponse, $actualResponse);
    }

    public function testExpiredTokenResultsInARetryAttemptWithADifferentToken()
    {
        $order = MerchantOrderRequestBuilder::makeCompleteRequest();

        $this->prepareTokenProviderWithAccessToken($this->expiredAccessToken);
        Phake::when($this->restTemplate)->get('gatekeeper/refresh')->thenReturn(json_encode($this->secondAccessToken));

        $this->connector->announceMerchantOrder($order);

        // Verify that a new access token is retrieved
        Phake::verify($this->restTemplate)->get('gatekeeper/refresh');

        // Verify that the correct token is used to call the API
        Phake::verify($this->restTemplate, Phake::never())->setToken($this->expiredAccessToken->getToken());
        Phake::verify($this->restTemplate)->setToken($this->secondAccessToken->getToken());

        // Verify that the new access token is stored in the token provider
        Phake::verify($this->tokenProvider)->setValue(TokenProvider::ACCESS_TOKEN, $this->secondAccessToken->getToken());
        Phake::verify($this->tokenProvider)->setValue(TokenProvider::ACCESS_TOKEN_VALID_UNTIL, $this->secondAccessToken->getValidUntil()->format(DateTime::ATOM));
        Phake::verify($this->tokenProvider)->setValue(TokenProvider::ACCESS_TOKEN_DURATION, $this->secondAccessToken->getDurationInMillis());
    }

    public function testNoAccessTokenProvided()
    {
        $order = MerchantOrderRequestBuilder::makeCompleteRequest();

        $this->prepareTokenProviderWithoutAccessToken();
        Phake::when($this->restTemplate)->get('gatekeeper/refresh')->thenReturn(json_encode($this->secondAccessToken));

        $this->connector->announceMerchantOrder($order);

        // Verify that a new access token is retrieved
        Phake::verify($this->restTemplate)->get('gatekeeper/refresh');

        // Verify that the correct token is used to call the API
        Phake::verify($this->restTemplate)->setToken($this->secondAccessToken->getToken());

        // Verify that the new access token is stored in the token provider
        Phake::verify($this->tokenProvider)->setValue(TokenProvider::ACCESS_TOKEN, $this->secondAccessToken->getToken());
        Phake::verify($this->tokenProvider)->setValue(TokenProvider::ACCESS_TOKEN_VALID_UNTIL, $this->secondAccessToken->getValidUntil()->format(DateTime::ATOM));
        Phake::verify($this->tokenProvider)->setValue(TokenProvider::ACCESS_TOKEN_DURATION, $this->secondAccessToken->getDurationInMillis());
    }

    private function prepareTokenProviderWithoutAccessToken()
    {
        $this->prepareTokenProviderWithAccessToken(null);
    }

    /**
     * @param AccessToken $accessToken
     */
    private function prepareTokenProviderWithAccessToken($accessToken)
    {
        $token = null;
        $validUntil = null;
        $durationInMillis = null;

        if (null !== $accessToken) {
            $token = $accessToken->getToken();
            $validUntil = $accessToken->getValidUntil()->format(DateTime::ATOM);
            $durationInMillis = $accessToken->getDurationInMillis();
        }

        Phake::when($this->tokenProvider)->getValue(TokenProvider::ACCESS_TOKEN)->thenReturn($token);
        Phake::when($this->tokenProvider)->getValue(TokenProvider::ACCESS_TOKEN_VALID_UNTIL)->thenReturn($validUntil);
        Phake::when($this->tokenProvider)->getValue(TokenProvider::ACCESS_TOKEN_DURATION)->thenReturn($durationInMillis);
    }

    /**
     * @param string $eventName
     *
     * @return string
     */
    private function makeAnnouncementResponse($eventName)
    {
        if ('merchant.order.status.changed' === $eventName) {
            return MerchantOrderStatusResponseBuilder::newInstanceAsJson();
        }
        throw new RuntimeException('Unknown announcement type');
    }
}
