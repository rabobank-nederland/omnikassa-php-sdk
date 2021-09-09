<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\AnnouncementResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\InvalidSignatureException;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SigningKey;

class AnnouncementResponseBuilder
{
    /**
     * @return AnnouncementResponse
     *
     * @throws \JsonMapper_Exception
     * @throws InvalidSignatureException
     */
    public static function newInstance()
    {
        return new AnnouncementResponse(json_encode(self::getTestData()), self::getSigningKey());
    }

    /**
     * @return AnnouncementResponse
     *
     * @throws \JsonMapper_Exception
     * @throws InvalidSignatureException
     */
    public static function invalidSignatureInstance()
    {
        $testData = self::getTestData();
        $testData['poiId'] = 100;

        return new AnnouncementResponse(json_encode($testData), self::getSigningKey());
    }

    /**
     * @return array
     */
    private static function getTestData()
    {
        return [
            'poiId' => 1000,
            'authentication' => 'MyJwt',
            'expiry' => '1970-01-01T00:00:00.000+02:00',
            'eventName' => 'merchant.order.status.changed',
            'signature' => 'ec0f64d23b91debd1249ee56b1b67540bf1720760cb23a9a286acc222724a8d15c7e33f193710e7e0322ced44d066f8cc6a2fdbd9398f36fb1f0a277431034aa',
        ];
    }

    /**
     * @return SigningKey
     */
    private static function getSigningKey()
    {
        return new SigningKey('secret');
    }
}
