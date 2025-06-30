<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use DateTime;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\RefundDetailsResponseBuilder;
use PHPUnit\Framework\TestCase;

class RefundDetailsResponseTest extends TestCase
{
    public function testThatObjectIsCorrectlyConstructed()
    {
        $responseInstance = RefundDetailsResponseBuilder::newInstance();
        $this->assertInstanceOf(RefundDetailsResponse::class, $responseInstance);

        $this->assertEquals('6fa74559-b95d-4d40-9fa9-e866e3c8e2d2', $responseInstance->getRefundId());
        $this->assertEquals('1e9bf154-e128-42a1-be8d-10f0174b4c3d', $responseInstance->getRefundTransactionId());

        $this->assertInstanceOf(DateTime::class, $responseInstance->getCreatedAt());
        $this->assertEquals('2022-06-20T12:37:37+00:00', $responseInstance->getCreatedAt()->format(DATE_ATOM));

        $this->assertInstanceOf(DateTime::class, $responseInstance->getUpdatedAt());
        $this->assertEquals('2022-01-15T12:34:56+00:00', $responseInstance->getUpdatedAt()->format(DATE_ATOM));

        $this->assertEquals('LOW', $responseInstance->getVatCategory());
        $this->assertEquals('MASTERCARD', $responseInstance->getPaymentBrand());
        $this->assertEquals('PENDING', $responseInstance->getStatus());
        $this->assertEquals('Dit is een test', $responseInstance->getDescription());
        $this->assertEquals('da1e7696-b199-4c87-83c3-9b34e00ba48e', $responseInstance->getTransactionId());

        $this->assertInstanceOf(Money::class, $responseInstance->getRefundMoney());
        $this->assertEquals('EUR', $responseInstance->getRefundMoney()->getCurrency());
        $this->assertEquals(10, $responseInstance->getRefundMoney()->getAmount());
    }

    public function testEmptyConstructor()
    {
        $responseInstance = new RefundDetailsResponse('');
        $this->assertInstanceOf(RefundDetailsResponse::class, $responseInstance);
    }

    public function testNullableFields()
    {
        $testData = RefundDetailsResponseBuilder::getTestData();

        // Just update the nullable fields.
        $testData['refundTransactionId'] = null;
        $testData['updatedAt'] = null;

        $responseInstance = new RefundDetailsResponse(json_encode($testData));
        $this->assertEquals(null, $responseInstance->getRefundTransactionId());
        $this->assertEquals(null, $responseInstance->getUpdatedAt());
    }
}
