<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\request;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Address;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\CustomerInformation;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\PaymentBrand;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\PaymentBrandForce;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\PaymentBrandMetaData;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request\MerchantOrder;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\OrderItemBuilder;

class MerchantOrderBuilder
{
    public static function makeMinimalOrder()
    {
        return MerchantOrder::createFrom([
            'merchantOrderId' => '100',
            'amount' => Money::fromDecimal('EUR', 99.99),
            'merchantReturnURL' => 'http://localhost/',
        ]);
    }

    public static function makeMinimalOrderWithSkipHppResultPage(bool $value)
    {
        return MerchantOrder::createFrom([
            'merchantOrderId' => '100',
            'amount' => Money::fromDecimal('EUR', 99.99),
            'merchantReturnURL' => 'http://localhost/',
            'skipHppResultPage' => $value,
        ]);
    }

    public static function makeMinimalOrderWithCustomerInformationFullName()
    {
        $customerInformation = CustomerInformation::createFrom([
            'fullName' => 'Jan van Veen',
        ]);

        return MerchantOrder::createFrom([
            'merchantOrderId' => '100',
            'amount' => Money::fromDecimal('EUR', 99.99),
            'merchantReturnURL' => 'http://localhost/',
            'customerInformation' => $customerInformation,
        ]);
    }

    public static function makeWithOrderItemsWithoutOptionalFields()
    {
        return MerchantOrder::createFrom([
            'merchantOrderId' => '100',
            'orderItems' => [OrderItemBuilder::makeOrderItemWithoutOptionals()],
            'amount' => Money::fromDecimal('EUR', 99.99),
            'merchantReturnURL' => 'http://localhost/',
        ]);
    }

    public static function makeWithShippingDetailsWithoutOptionalFields()
    {
        $shippingDetail = Address::createFrom([
            'firstName' => 'Jan',
            'middleName' => 'van',
            'lastName' => 'Veen',
            'street' => 'Voorbeeldstraat',
            'postalCode' => '1234AB',
            'city' => 'Haarlem',
            'countryCode' => 'NL',
            'houseNumber' => '5',
            'houseNumberAddition' => 'a',
        ]);

        return MerchantOrder::createFrom([
            'merchantOrderId' => '100',
            'shippingDetail' => $shippingDetail,
            'amount' => Money::fromDecimal('EUR', 99.99),
            'merchantReturnURL' => 'http://localhost/',
        ]);
    }

    public static function makeWithPaymentBrandRestrictionButWithoutOtherOptionalFields($paymentBrand, $paymentBrandForce)
    {
        return MerchantOrder::createFrom([
            'merchantOrderId' => '100',
            'amount' => Money::fromDecimal('EUR', 99.99),
            'merchantReturnURL' => 'http://localhost/',
            'paymentBrand' => $paymentBrand,
            'paymentBrandForce' => $paymentBrandForce,
        ]);
    }

    public static function makeCompleteOrder()
    {
        $shippingDetail = Address::createFrom([
            'firstName' => 'Jan',
            'middleName' => 'van',
            'lastName' => 'Veen',
            'street' => 'Voorbeeldstraat',
            'postalCode' => '1234AB',
            'city' => 'Haarlem',
            'countryCode' => 'NL',
            'houseNumber' => '5',
            'houseNumberAddition' => 'a',
        ]);

        $billingDetail = Address::createFrom([
            'firstName' => 'Piet',
            'middleName' => 'van der',
            'lastName' => 'Stoel',
            'street' => 'Dorpsstraat',
            'postalCode' => '4321YZ',
            'city' => 'Bennebroek',
            'countryCode' => 'NL',
            'houseNumber' => '9',
            'houseNumberAddition' => 'rood',
        ]);

        $customerInformation = CustomerInformation::createFrom([
            'emailAddress' => 'jan.van.veen@gmail.com',
            'dateOfBirth' => '20-03-1987',
            'gender' => 'M',
            'initials' => 'J.M.',
            'telephoneNumber' => '0204971111',
            'fullName' => 'Jan van Veen',
        ]);

        $paymentBrandMetaData = PaymentBrandMetaData::createFrom([
            'issuerId' => 'RABONL2U',
        ]);

        return MerchantOrder::createFrom([
            'merchantOrderId' => '100',
            'description' => 'Order ID: 100',
            'orderItems' => [OrderItemBuilder::makeCompleteOrderItem()],
            'amount' => Money::fromDecimal('EUR', 99.99),
            'shippingDetail' => $shippingDetail,
            'billingDetail' => $billingDetail,
            'customerInformation' => $customerInformation,
            'language' => 'NL',
            'merchantReturnURL' => 'http://localhost/',
            'paymentBrand' => PaymentBrand::IDEAL,
            'paymentBrandForce' => PaymentBrandForce::FORCE_ONCE,
            'skipHppResultPage' => false,
            'paymentBrandMetaData' => $paymentBrandMetaData,
        ]);
    }

    public static function makeMinimalOrderWithMetaData(array $data)
    {
        $paymentBrandMetaData = PaymentBrandMetaData::createFrom($data);

        return MerchantOrder::createFrom([
            'merchantOrderId' => '100',
            'amount' => Money::fromDecimal('EUR', 99.99),
            'merchantReturnURL' => 'http://localhost/',
            'paymentBrandMetaData' => $paymentBrandMetaData,
        ]);
    }
}
