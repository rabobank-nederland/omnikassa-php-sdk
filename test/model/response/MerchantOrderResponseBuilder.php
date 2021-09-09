<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\MerchantOrderResponse;

class MerchantOrderResponseBuilder
{
    /**
     * @return MerchantOrderResponse
     *
     * @throws \JsonMapper_Exception
     */
    public static function newInstance()
    {
        return new MerchantOrderResponse(json_encode(self::getData()));
    }

    /**
     * @return string
     */
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
            'redirectUrl' => 'http://localhost/redirect/url',
            'omnikassaOrderId' => 'c9f48056-404a-456e-87ee-ece08e98b271',
        ];
    }
}
