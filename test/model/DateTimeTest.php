<?php namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model;

use DateTime;
use PHPUnit_Framework_TestCase;

class DateTimeTest extends PHPUnit_Framework_TestCase
{
    public function testDateTimeConversions()
    {
        $expectedPhpDateTime = new DateTime('2016-10-27T10:36:51.123+0200');

        $actualJacksonConvertedDateTime = new DateTime('2016-10-27T08:36:51.123+0000');
        $actualJodaConvertedDateTime = new DateTime('2016-10-27T10:36:51.123+02:00');

        $this->dumpDate($expectedPhpDateTime);
        $this->dumpDate($actualJacksonConvertedDateTime);
        $this->dumpDate($actualJodaConvertedDateTime);

        $this->assertEquals($expectedPhpDateTime, $actualJacksonConvertedDateTime, 'Jackson conversion failed');
        $this->assertEquals($expectedPhpDateTime, $actualJodaConvertedDateTime, 'Joda conversion failed');
    }

    /**
     * @param $expectedPhpDateTime
     */
    private function dumpDate(DateTime $expectedPhpDateTime)
    {
        echo 'Year: ' . $expectedPhpDateTime->format('Y') . "\n";
        echo 'Month: ' . $expectedPhpDateTime->format('m') . "\n";
        echo 'Day: ' . $expectedPhpDateTime->format('d') . "\n";
        echo 'Hour: ' . $expectedPhpDateTime->format('H') . "\n";
        echo 'Minute: ' . $expectedPhpDateTime->format('i') . "\n";
        echo 'Second: ' . $expectedPhpDateTime->format('s') . "\n";
        echo 'Millisecond: ' . $expectedPhpDateTime->format('u') . "\n";
        echo 'Timezone: ' . $expectedPhpDateTime->format('e') . "\n\n";
    }
}