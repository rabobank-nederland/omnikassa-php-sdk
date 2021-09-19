<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\request;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\PaymentBrand;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\PaymentBrandForce;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request\MerchantOrderRequest;

class MerchantOrderRequestBuilder
{
    /**
     * @return MerchantOrderRequest
     */
    public static function makeCompleteRequest()
    {
        return new MerchantOrderRequest(MerchantOrderBuilder::makeCompleteOrder());
    }

    public static function makeWithOrderItemsWithoutOptionalFieldsRequest()
    {
        return new MerchantOrderRequest(MerchantOrderBuilder::makeWithOrderItemsWithoutOptionalFields());
    }

    public static function makeWithShippingDetailsWithoutOptionalFieldsRequest()
    {
        return new MerchantOrderRequest(MerchantOrderBuilder::makeWithShippingDetailsWithoutOptionalFields());
    }

    public static function makeWithPaymentBrandButWithoutOtherOptionalFields()
    {
        return new MerchantOrderRequest(MerchantOrderBuilder::makeWithPaymentBrandRestrictionButWithoutOtherOptionalFields(PaymentBrand::IDEAL, PaymentBrandForce::FORCE_ONCE));
    }

    /**
     * @return MerchantOrderRequest
     */
    public static function makeMinimalRequest()
    {
        return new MerchantOrderRequest(MerchantOrderBuilder::makeMinimalOrder());
    }

    public static function makeMinimalRequestWithSkipHppResultPage(bool $value)
    {
        return new MerchantOrderRequest(MerchantOrderBuilder::makeMinimalOrderWithSkipHppResultPage($value));
    }

    public static function makeMinimalRequestWithCustomerInformationFullName()
    {
        return new MerchantOrderRequest(MerchantOrderBuilder::makeMinimalOrderWithCustomerInformationFullName());
    }

    public static function makeMinimalRequestWithMetaData(array $data)
    {
        return new MerchantOrderRequest(MerchantOrderBuilder::makeMinimalOrderWithMetaData($data));
    }
}
