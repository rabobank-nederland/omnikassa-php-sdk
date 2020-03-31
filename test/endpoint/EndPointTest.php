<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\endpoint;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\Connector;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SigningKey;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\endpoint\EndpointWrapper;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\request\MerchantOrderBuilder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\AnnouncementResponseBuilder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\MerchantOrderResponseBuilder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\MerchantOrderStatusResponseBuilder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\PaymentBrandsResponseBuilder;
use Phake;

class EndpointTest extends \PHPUnit_Framework_TestCase
{
    /** @var Endpoint */
    private $endpoint;
    /** @var Connector */
    private $connector;

    private $signingKey;

    public function setUp()
    {
        $this->signingKey = new SigningKey('secret');
        $this->connector = Phake::mock(Connector::class);
        $this->endpoint = new EndpointWrapper($this->connector, $this->signingKey);
    }

    public function testAnnounceMerchantOrder()
    {
        $merchantOrder = MerchantOrderBuilder::makeCompleteOrder();

        Phake::when($this->connector)->announceMerchantOrder->thenReturn(MerchantOrderResponseBuilder::newInstanceAsJson());

        $result = $this->endpoint->announceMerchantOrder($merchantOrder);

        $this->assertEquals('http://localhost/redirect/url', $result);
    }

    public function testAnnounce()
    {
        $merchantOrder = MerchantOrderBuilder::makeCompleteOrder();

        Phake::when($this->connector)->announceMerchantOrder->thenReturn(MerchantOrderResponseBuilder::newInstanceAsJson());

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

    public function testRetrievePaymentBrandsInfo() {

        Phake::when($this->connector)->getPaymentBrands()->thenReturn(PaymentBrandsResponseBuilder::newInstanceAsJson());

        $result = $this->endpoint->retrievePaymentBrands();

        $this->assertEquals(PaymentBrandsResponseBuilder::newInstance(), $result);
    }
}
