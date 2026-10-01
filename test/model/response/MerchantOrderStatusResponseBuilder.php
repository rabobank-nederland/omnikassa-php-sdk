<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response;

use JsonMapper_Exception;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\MerchantOrderStatusResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\InvalidSignatureException;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SigningKey;

class MerchantOrderStatusResponseBuilder
{
    /**
     * @return MerchantOrderStatusResponse
     *
     * @throws JsonMapper_Exception
     * @throws InvalidSignatureException
     */
    public static function newInstance()
    {
        return new MerchantOrderStatusResponse(json_encode(self::getData()), self::getSigningKey());
    }

    /**
     * @return MerchantOrderStatusResponse
     *
     * @throws JsonMapper_Exception
     * @throws InvalidSignatureException
     */
    public static function invalidSignatureInstance()
    {
        $testData = self::getData();
        $testData['moreOrderResultsAvailable'] = false;

        return new MerchantOrderStatusResponse(json_encode($testData), self::getSigningKey());
    }

    /** @return string */
    public static function newInstanceAsJson()
    {
        return json_encode(self::getData());
    }

    /**
     * @return array
     */
    private static function getData()
    {
        return [
            'moreOrderResultsAvailable' => true,
            'orderResults' => [
                [
                    'poiId' => 1000,
                    'merchantOrderId' => '10',
                    'omnikassaOrderId' => '1',
                    'orderStatus' => 'CANCELLED',
                    'orderStatusDateTime' => '1970-01-01T00:00:00.000+02:00',
                    'errorCode' => '666',
                    'paidAmount' => [
                        'currency' => 'EUR',
                        'amount' => 100,
                    ],
                    'totalAmount' => [
                        'currency' => 'EUR',
                        'amount' => 100,
                    ],
                    'transactions' => [
                        [
                            'id' => '791fdcda-65e4-4f81-8b45-988404da3a8d',
                            'paymentBrand' => 'IDEAL',
                            'type' => 'PAYMENT',
                            'status' => 'SUCCESS',
                            'amount' => ['currency' => 'EUR', 'amount' => 100],
                            'confirmedAmount' => ['currency' => 'EUR', 'amount' => 100],
                            'startTime' => '2026-02-17T11:15:29.244+01:00',
                            'lastUpdateTime' => '2026-02-17T11:15:32.698+01:00',
                        ],
                    ],
                ],
            ],
            'signature' => 'd2404e34db4f2cb65852a5e6b65cf53b72d262f660790f22bafa1c2c9c76ea237c0705352e0bb34bb09f9f83b3536fb3650e2bad36457ad34d2dad04094355ef',
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
