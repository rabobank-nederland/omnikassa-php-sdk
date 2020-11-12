<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\MerchantOrderResponseBuilder;
use PHPUnit\Framework\TestCase;

class MerchantOrderResponseTest extends TestCase
{
    public function testThatObjectIsCorrectlyConstructed()
    {
        $merchantOrderResponse = MerchantOrderResponseBuilder::newInstance();

        $this->assertEquals('http://localhost/redirect/url', $merchantOrderResponse->getRedirectUrl());
        $this->assertEquals('c9f48056-404a-456e-87ee-ece08e98b271', $merchantOrderResponse->getOmnikassaOrderId());
    }
}
