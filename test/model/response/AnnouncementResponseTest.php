<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\AnnouncementResponseBuilder;
use PHPUnit\Framework\TestCase;

class AnnouncementResponseTest extends TestCase
{
    public function testThatObjectIsCorrectlyConstructed()
    {
        $response = AnnouncementResponseBuilder::newInstance();

        $this->assertEquals(1000, $response->getPoiId());
        $this->assertEquals('MyJwt', $response->getAuthentication());
        $this->assertEquals('1970-01-01T00:00:00.000+02:00', $response->getExpiry());
        $this->assertEquals('merchant.order.status.changed', $response->getEventName());
    }

    /**
     * @expectedException \ErrorException
     * @expectedExceptionMessage The signature validation of the response failed. Please contact the Rabobank service team.
     */
    public function testThatInvalidSignatureExceptionIsThrownWhenTheSignaturesDoNotMatch()
    {
        $response = AnnouncementResponseBuilder::invalidSignatureInstance();
    }
}
