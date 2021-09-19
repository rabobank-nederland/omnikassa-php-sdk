<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\PaymentBrandsResponse;

class PaymentBrandsResponseBuilder
{
    /**
     * @return PaymentBrandsResponse
     *
     * @throws \JsonMapper_Exception
     */
    public static function newInstance()
    {
        return new PaymentBrandsResponse(json_encode(self::getTestData()));
    }

    /**
     * @return PaymentBrandsResponse
     *
     * @throws \JsonMapper_Exception
     */
    public static function newInstanceAsJson()
    {
        return json_encode(self::getTestData());
    }

    private static function getTestData()
    {
        return [
            'paymentBrands' => [
                ['name' => 'IDEAL', 'status' => 'Active'],
                ['name' => 'PAYPAL', 'status' => 'Active'],
                ['name' => 'AFTERPAY', 'status' => 'Inactive'],
            ],
        ];
    }
}
