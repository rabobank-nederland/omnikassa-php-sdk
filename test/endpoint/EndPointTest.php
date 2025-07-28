<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\endpoint;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\Connector;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\TokenProvider;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\RefundDetailsResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\TransactionRefundableDetailsResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SigningKey;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\endpoint\EndpointWrapper;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\request\InitiateRefundRequestBuilder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\request\MerchantOrderBuilder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\AnnouncementResponseBuilder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\IdealIssuersResponseBuilder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\MerchantOrderResponseBuilder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\MerchantOrderStatusResponseBuilder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\PaymentBrandsResponseBuilder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\RefundDetailsResponseBuilder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\TransactionRefundableDetailsResponseBuilder;
use Phake;
use PHPUnit\Framework\TestCase;

class EndPointTest extends TestCase
{
    /** @var Endpoint */
    private $endpoint;
    /** @var Connector */
    private $connector;
    /** @var SigningKey */
    private $signingKey;

    protected function setUp(): void
    {
        $this->signingKey = new SigningKey('secret');
        $this->connector = Phake::mock(Connector::class);
        $this->endpoint = new EndpointWrapper($this->connector, $this->signingKey);
    }

    public function testCreateInstance()
    {
        $mockedTokenProvider = Phake::mock(TokenProvider::class);
        $instance = Endpoint::createInstance('https://localhost/', $this->signingKey, $mockedTokenProvider);

        $this->assertInstanceOf(Endpoint::class, $instance);
    }

    /**
     * @deprecated See testAnnounceMerchantOrder()
     */
    public function testDeprecatedAnnounceMerchantOrder()
    {
        $merchantOrder = MerchantOrderBuilder::makeCompleteOrder();

        // Note: The connector internally already uses announce() as an alias
        Phake::when($this->connector)->announce->thenReturn(MerchantOrderResponseBuilder::newInstanceAsJson());

        $result = $this->endpoint->announceMerchantOrder($merchantOrder);

        $this->assertEquals('http://localhost/redirect/url', $result);
    }

    public function testAnnounce()
    {
        $merchantOrder = MerchantOrderBuilder::makeCompleteOrder();

        Phake::when($this->connector)->announce->thenReturn(MerchantOrderResponseBuilder::newInstanceAsJson());

        $result = $this->endpoint->announce($merchantOrder);

        $this->assertEquals(MerchantOrderResponseBuilder::newInstance(), $result);
    }

    public function testRetrieveAnnouncement()
    {
        $announcementResponse = AnnouncementResponseBuilder::newInstance();
        $merchantOrderResponse = MerchantOrderStatusResponseBuilder::newInstance();
        $merchantOrderResponseAsJson = MerchantOrderStatusResponseBuilder::newInstanceAsJson();

        Phake::when($this->connector)->getAnnouncementData($announcementResponse)->thenReturn($merchantOrderResponseAsJson);

        $result = $this->endpoint->retrieveAnnouncement($announcementResponse);

        $this->assertEquals($merchantOrderResponse, $result);
    }

    public function testInitiateRefundTransaction()
    {
        $transactionId = 'da1e7696-b199-4c87-83c3-9b34e00ba48e';
        $merchantRequestReference = '9fd4eb02-84da-11ec-a9f1-973e359e11d4';

        $initiateRefundRequest = InitiateRefundRequestBuilder::makeFullRequest();
        $refundDetailsResponse = RefundDetailsResponseBuilder::newInstance();
        $refundDetailsResponseAsJson = RefundDetailsResponseBuilder::newInstanceAsJson();

        Phake::when($this->connector)->postRefundRequest($initiateRefundRequest, $transactionId, $merchantRequestReference)
            ->thenReturn($refundDetailsResponseAsJson);
        $result = $this->endpoint->initiateRefundTransaction(
            $initiateRefundRequest,
            $transactionId,
            $merchantRequestReference
        );

        $this->assertInstanceOf(RefundDetailsResponse::class, $result);
        $this->assertEquals($refundDetailsResponse, $result);
    }

    public function testFetchRefundTransaction()
    {
        $transactionId = 'da1e7696-b199-4c87-83c3-9b34e00ba48e';
        $refundId = '6fa74559-b95d-4d40-9fa9-e866e3c8e2d2';
        $refundDetailsResponse = RefundDetailsResponseBuilder::newInstance();
        $refundDetailsResponseAsJson = RefundDetailsResponseBuilder::newInstanceAsJson();

        Phake::when($this->connector)->getRefundRequest($transactionId, $refundId)->thenReturn($refundDetailsResponseAsJson);

        $result = $this->endpoint->fetchRefundTransaction($transactionId, $refundId);

        $this->assertInstanceOf(RefundDetailsResponse::class, $result);
        $this->assertEquals($refundDetailsResponse, $result);
    }

    public function testFetchRefundableTransactionDetails()
    {
        $transactionId = 'da1e7696-b199-4c87-83c3-9b34e00ba48e';
        $refundableDetailsResponse = TransactionRefundableDetailsResponseBuilder::newInstance();
        $refundableDetailsResponseAsJson = TransactionRefundableDetailsResponseBuilder::newInstanceAsJson();

        Phake::when($this->connector)->getRefundableDetails($transactionId)->thenReturn($refundableDetailsResponseAsJson);

        $result = $this->endpoint->fetchRefundableTransactionDetails($transactionId);

        $this->assertInstanceOf(TransactionRefundableDetailsResponse::class, $result);
        $this->assertEquals($refundableDetailsResponse, $result);
    }

    public function testRetrievePaymentBrandsInfo()
    {
        Phake::when($this->connector)->getPaymentBrands()->thenReturn(PaymentBrandsResponseBuilder::newInstanceAsJson());

        $result = $this->endpoint->retrievePaymentBrands();

        $this->assertEquals(PaymentBrandsResponseBuilder::newInstance(), $result);
    }

    public function testRetrieveIDEALIssuers(): void
    {
        Phake::when($this->connector)->getIDEALIssuers()->thenReturn(IdealIssuersResponseBuilder::newInstanceAsJson());

        $result = $this->endpoint->retrieveIDEALIssuers();

        $this->assertEquals(IdealIssuersResponseBuilder::newInstance(), $result);
    }
}
