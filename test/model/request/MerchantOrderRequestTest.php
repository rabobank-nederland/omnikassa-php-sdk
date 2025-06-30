<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request;

use DateTime;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\ProductType;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\VatCategory;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\request\MerchantOrderRequestBuilder;
use PHPUnit\Framework\TestCase;
use stdClass;

class MerchantOrderRequestTest extends TestCase
{
    public function testJsonEncodingWithoutOptionalFields()
    {
        $merchantOrderRequest = MerchantOrderRequestBuilder::makeMinimalRequest();
        $merchantOrderRequest->setTimestamp($this->createTimestamp());
        $expectedJson = json_encode([
            'timestamp' => '2016-12-21T14:13:56+01:00',
            'merchantOrderId' => '100',
            'amount' => ['currency' => 'EUR', 'amount' => 9999],
            'merchantReturnURL' => 'http://localhost/',
            'skipHppResultPage' => false,
        ]);

        $actualJson = json_encode($merchantOrderRequest);

        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testExceptionIsThrownForInvalidProperty()
    {
        $this->expectException('InvalidArgumentException');

        MerchantOrder::createFrom(['merchanOrderId' => 'test']);
    }

    public function testJsonEncodingAllFields()
    {
        $merchantOrderRequest = MerchantOrderRequestBuilder::makeCompleteRequest();
        $merchantOrderRequest->setTimestamp($this->createTimestamp());
        $expectedJson = json_encode([
            'timestamp' => '2016-12-21T14:13:56+01:00',
            'merchantOrderId' => '100',
            'description' => 'Order ID: 100',
            'orderItems' => [
                ['id' => '15', 'name' => 'Name', 'description' => 'Description', 'quantity' => 1, 'amount' => ['currency' => 'EUR', 'amount' => 100], 'tax' => ['currency' => 'EUR', 'amount' => 50], 'category' => ProductType::DIGITAL, 'vatCategory' => VatCategory::LOW],
            ],
            'amount' => ['currency' => 'EUR', 'amount' => 9999],
            'shippingDetail' => ['firstName' => 'Jan', 'middleName' => 'van', 'lastName' => 'Veen', 'street' => 'Voorbeeldstraat', 'houseNumber' => '5', 'houseNumberAddition' => 'a', 'postalCode' => '1234AB', 'city' => 'Haarlem', 'countryCode' => 'NL'],
            'billingDetail' => ['firstName' => 'Piet', 'middleName' => 'van der', 'lastName' => 'Stoel', 'street' => 'Dorpsstraat', 'houseNumber' => '9', 'houseNumberAddition' => 'rood', 'postalCode' => '4321YZ', 'city' => 'Bennebroek', 'countryCode' => 'NL'],
            'customerInformation' => ['emailAddress' => 'jan.van.veen@gmail.com', 'dateOfBirth' => '20-03-1987', 'gender' => 'M', 'initials' => 'J.M.', 'telephoneNumber' => '0204971111', 'fullName' => 'Jan van Veen'],
            'language' => 'NL',
            'merchantReturnURL' => 'http://localhost/',
            'paymentBrand' => 'IDEAL',
            'paymentBrandForce' => 'FORCE_ONCE',
            'skipHppResultPage' => false,
            'paymentBrandMetaData' => [
                'issuerId' => 'RABONL2U',
            ],
        ]);

        $actualJson = json_encode($merchantOrderRequest);

        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testJsonEncodingWithOrderItemsWithoutOptionalFields()
    {
        $merchantOrderRequest = MerchantOrderRequestBuilder::makeWithOrderItemsWithoutOptionalFieldsRequest();
        $merchantOrderRequest->setTimestamp($this->createTimestamp());
        $expectedJson = json_encode([
            'timestamp' => '2016-12-21T14:13:56+01:00',
            'merchantOrderId' => '100',
            'orderItems' => [
                ['name' => 'Name', 'description' => 'Description', 'quantity' => 1, 'amount' => ['currency' => 'EUR', 'amount' => 100], 'category' => ProductType::DIGITAL],
            ],
            'amount' => ['currency' => 'EUR', 'amount' => 9999],
            'merchantReturnURL' => 'http://localhost/',
            'skipHppResultPage' => false,
        ]);

        $actualJson = json_encode($merchantOrderRequest);

        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testJsonEncodingWithShippingDetailsWithoutOptionalFields()
    {
        $merchantOrderRequest = MerchantOrderRequestBuilder::makeWithShippingDetailsWithoutOptionalFieldsRequest();
        $merchantOrderRequest->setTimestamp($this->createTimestamp());
        $expectedJson = json_encode([
            'timestamp' => '2016-12-21T14:13:56+01:00',
            'merchantOrderId' => '100',
            'amount' => ['currency' => 'EUR', 'amount' => 9999],
            'shippingDetail' => ['firstName' => 'Jan', 'middleName' => 'van', 'lastName' => 'Veen', 'street' => 'Voorbeeldstraat', 'houseNumber' => '5', 'houseNumberAddition' => 'a', 'postalCode' => '1234AB', 'city' => 'Haarlem', 'countryCode' => 'NL'],
            'merchantReturnURL' => 'http://localhost/',
            'skipHppResultPage' => false,
        ]);

        $actualJson = json_encode($merchantOrderRequest);

        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testJsonEncodingWithPaymentBrandButWithoutOtherOptionalFields()
    {
        $merchantOrderRequest = MerchantOrderRequestBuilder::makeWithPaymentBrandButWithoutOtherOptionalFields();
        $merchantOrderRequest->setTimestamp($this->createTimestamp());
        $expectedJson = json_encode([
            'timestamp' => '2016-12-21T14:13:56+01:00',
            'merchantOrderId' => '100',
            'amount' => ['currency' => 'EUR', 'amount' => 9999],
            'merchantReturnURL' => 'http://localhost/',
            'paymentBrand' => 'IDEAL',
            'paymentBrandForce' => 'FORCE_ONCE',
            'skipHppResultPage' => false,
        ]);

        $actualJson = json_encode($merchantOrderRequest);

        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testJsonEncodingWithTrueSkipHppResultPage()
    {
        $merchantOrderRequest = MerchantOrderRequestBuilder::makeMinimalRequestWithSkipHppResultPage(true);
        $merchantOrderRequest->setTimestamp($this->createTimestamp());
        $expectedJson = json_encode([
            'timestamp' => '2016-12-21T14:13:56+01:00',
            'merchantOrderId' => '100',
            'amount' => ['currency' => 'EUR', 'amount' => 9999],
            'merchantReturnURL' => 'http://localhost/',
            'skipHppResultPage' => true,
        ]);

        $actualJson = json_encode($merchantOrderRequest);

        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testJsonEncodingWithFalseSkipHppResultPage()
    {
        $merchantOrderRequest = MerchantOrderRequestBuilder::makeMinimalRequestWithSkipHppResultPage(false);
        $merchantOrderRequest->setTimestamp($this->createTimestamp());
        $expectedJson = json_encode([
            'timestamp' => '2016-12-21T14:13:56+01:00',
            'merchantOrderId' => '100',
            'amount' => ['currency' => 'EUR', 'amount' => 9999],
            'merchantReturnURL' => 'http://localhost/',
            'skipHppResultPage' => false,
        ]);

        $actualJson = json_encode($merchantOrderRequest);

        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testJsonEncodingWithCustomerInformationFullName()
    {
        $merchantOrderRequest = MerchantOrderRequestBuilder::makeMinimalRequestWithCustomerInformationFullName();
        $merchantOrderRequest->setTimestamp($this->createTimestamp());
        $expectedJson = json_encode([
            'timestamp' => '2016-12-21T14:13:56+01:00',
            'merchantOrderId' => '100',
            'amount' => ['currency' => 'EUR', 'amount' => 9999],
            'customerInformation' => [
                'fullName' => 'Jan van Veen',
            ],
            'merchantReturnURL' => 'http://localhost/',
            'skipHppResultPage' => false,
        ]);

        $actualJson = json_encode($merchantOrderRequest);

        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testJsonEncodingWithInvalidMetaData()
    {
        $merchantOrderRequest = MerchantOrderRequestBuilder::makeMinimalRequestWithMetaData([
            0 => 'Test', // Only string keys allowed
            'key1' => [], // Arrays are not allowed as value
            'key2' => new stdClass(), // Objects are not allowed as value
        ]);
        $merchantOrderRequest->setTimestamp($this->createTimestamp());
        $expectedJson = json_encode([
            'timestamp' => '2016-12-21T14:13:56+01:00',
            'merchantOrderId' => '100',
            'amount' => ['currency' => 'EUR', 'amount' => 9999],
            'merchantReturnURL' => 'http://localhost/',
            'skipHppResultPage' => false,
        ]);

        $actualJson = json_encode($merchantOrderRequest);

        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testJsonEncodingWithMultipleMetaDataEntries()
    {
        $merchantOrderRequest = MerchantOrderRequestBuilder::makeMinimalRequestWithMetaData([
            'test1' => 'Test1',
            'test4' => 'Test4',
            'test5' => 'Test5',
            'test7' => 'Test7',
        ]);
        $merchantOrderRequest->setTimestamp($this->createTimestamp());
        $expectedJson = json_encode([
            'timestamp' => '2016-12-21T14:13:56+01:00',
            'merchantOrderId' => '100',
            'amount' => ['currency' => 'EUR', 'amount' => 9999],
            'merchantReturnURL' => 'http://localhost/',
            'skipHppResultPage' => false,
            'paymentBrandMetaData' => [
                'test1' => 'Test1',
                'test4' => 'Test4',
                'test5' => 'Test5',
                'test7' => 'Test7',
            ],
        ]);

        $actualJson = json_encode($merchantOrderRequest);

        $this->assertEquals($expectedJson, $actualJson);
    }

    /**
     * @return DateTime
     */
    private function createTimestamp()
    {
        return new DateTime('2016-12-21T14:13:56+01:00');
    }
}
