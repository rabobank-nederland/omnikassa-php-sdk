<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model;

use DateTime;
use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase
{
    public function testDateTimeConversions()
    {
        $expectedPhpDateTime = new DateTime('2016-10-27T10:36:51.123+0200');

        $actualJacksonConvertedDateTime = new DateTime('2016-10-27T08:36:51.123+0000');
        $actualJodaConvertedDateTime = new DateTime('2016-10-27T10:36:51.123+02:00');

        $this->assertEquals($expectedPhpDateTime, $actualJacksonConvertedDateTime, 'Jackson conversion failed');
        $this->assertEquals($expectedPhpDateTime, $actualJodaConvertedDateTime, 'Joda conversion failed');
    }
}
