<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\IdealIssuersResponseBuilder;
use PHPUnit\Framework\TestCase;

class IdealIssuersResponseTest extends TestCase
{
    public function testThatObjectIsCorrectlyConstructed(): void
    {
        $idealIssuersResponse = IdealIssuersResponseBuilder::newInstance();

        $this->assertCount(2, $idealIssuersResponse->getIssuers());

        $this->assertEquals('BANKNL2Y', $idealIssuersResponse->getIssuers()[0]->getId());
        $this->assertEquals('iDEAL issuer simulatie', $idealIssuersResponse->getIssuers()[0]->getName());
        $this->assertEquals('Nederland', $idealIssuersResponse->getIssuers()[0]->getCountryNames());
        $this->assertCount(1, $idealIssuersResponse->getIssuers()[0]->getLogos());
        $this->assertEquals('https://betalen-acpt3.rabobank.nl/omnikassa/static/issuers/BANKNL2Y.png', $idealIssuersResponse->getIssuers()[0]->getLogos()[0]->getUrl());
        $this->assertEquals('image/png', $idealIssuersResponse->getIssuers()[0]->getLogos()[0]->getMimeType());

        $this->assertEquals('RABONL2U', $idealIssuersResponse->getIssuers()[1]->getId());
        $this->assertEquals('RABONL2U - eWL issuer simluation', $idealIssuersResponse->getIssuers()[1]->getName());
        $this->assertEquals('Nederland', $idealIssuersResponse->getIssuers()[1]->getCountryNames());
        $this->assertCount(1, $idealIssuersResponse->getIssuers()[1]->getLogos());
        $this->assertEquals('https://betalen-acpt3.rabobank.nl/omnikassa/static/issuers/RABONL2U.png', $idealIssuersResponse->getIssuers()[1]->getLogos()[0]->getUrl());
        $this->assertEquals('image/png', $idealIssuersResponse->getIssuers()[1]->getLogos()[0]->getMimeType());
    }
}
