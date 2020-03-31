<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\MerchantOrderStatusResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\InvalidSignatureException;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SigningKey;

class MerchantOrderStatusResponseBuilder
{
    /**
     * @return MerchantOrderStatusResponse
     *
     * @throws \JsonMapper_Exception
     * @throws InvalidSignatureException
     */
    public static function newInstance()
    {
        return new MerchantOrderStatusResponse(json_encode(self::getData()), self::getSigningKey());
    }

    /**
     * @return MerchantOrderStatusResponse
     *
     * @throws \JsonMapper_Exception
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
                ],
            ],
            'signature' => 'ddad03e536719f988a46f7edaf5808446838109b1644a13cef9e0e0f74825a70df618d325f8ce6eb09d629a70b6a0728f99fb8e85f249ca76636d7c13d54b841',
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
