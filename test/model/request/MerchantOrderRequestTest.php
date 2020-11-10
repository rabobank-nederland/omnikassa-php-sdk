<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\ProductType;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SigningKey;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\VatCategory;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\request\MerchantOrderRequestBuilder;
use PHPUnit\Framework\TestCase;

class MerchantOrderRequestTest extends TestCase
{
    public function testJsonEncoding_withoutOptionalFields()
    {
        $merchantOrderRequest = MerchantOrderRequestBuilder::makeMinimalRequest();
        $merchantOrderRequest->setTimestamp($this->createTimestamp());
        $expectedJson = json_encode([
            'timestamp' => '2016-12-21T14:13:56+01:00',
            'merchantOrderId' => '100',
            'amount' => ['currency' => 'EUR', 'amount' => 9999],
            'merchantReturnURL' => 'http://localhost/',
        ]);

        $actualJson = json_encode($merchantOrderRequest);

        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testExceptionIsThrownForInvalidProperty()
    {
        $this->expectException('InvalidArgumentException');

        MerchantOrder::createFrom(['merchanOrderId' => 'test']);
    }

    public function testJsonEncoding_allFields()
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
            'customerInformation' => ['emailAddress' => 'jan.van.veen@gmail.com', 'dateOfBirth' => '20-03-1987', 'gender' => 'M', 'initials' => 'J.M.', 'telephoneNumber' => '0204971111'],
            'language' => 'NL',
            'merchantReturnURL' => 'http://localhost/',
            'paymentBrand' => 'IDEAL',
            'paymentBrandForce' => 'FORCE_ONCE',
        ]);

        $actualJson = json_encode($merchantOrderRequest);

        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testJsonEncoding_withOrderItemsWithoutOptionalFields()
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
        ]);

        $actualJson = json_encode($merchantOrderRequest);

        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testJsonEncoding_withShippingDetailsWithoutOptionalFields()
    {
        $merchantOrderRequest = MerchantOrderRequestBuilder::makeWithShippingDetailsWithoutOptionalFieldsRequest();
        $merchantOrderRequest->setTimestamp($this->createTimestamp());
        $expectedJson = json_encode([
            'timestamp' => '2016-12-21T14:13:56+01:00',
            'merchantOrderId' => '100',
            'amount' => ['currency' => 'EUR', 'amount' => 9999],
            'shippingDetail' => ['firstName' => 'Jan', 'middleName' => 'van', 'lastName' => 'Veen', 'street' => 'Voorbeeldstraat', 'houseNumber' => '5', 'houseNumberAddition' => 'a', 'postalCode' => '1234AB', 'city' => 'Haarlem', 'countryCode' => 'NL'],
            'merchantReturnURL' => 'http://localhost/',
        ]);

        $actualJson = json_encode($merchantOrderRequest);

        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testJsonEncoding_withPaymentBrandButWithoutOtherOptionalFields()
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
        ]);

        $actualJson = json_encode($merchantOrderRequest);

        $this->assertEquals($expectedJson, $actualJson);
    }

    /**
     * @return \DateTime
     */
    private function createTimestamp()
    {
        return new \DateTime('2016-12-21T14:13:56+01:00');
    }
}
