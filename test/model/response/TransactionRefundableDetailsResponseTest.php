<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use DateTime;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\TransactionRefundableDetailsResponseBuilder;
use PHPUnit\Framework\TestCase;

class TransactionRefundableDetailsResponseTest extends TestCase
{
    public function testThatObjectIsCorrectlyConstructed()
    {
        $responseInstance = TransactionRefundableDetailsResponseBuilder::newInstance();
        $this->assertInstanceOf(TransactionRefundableDetailsResponse::class, $responseInstance);

        $this->assertEquals('da1e7696-b199-4c87-83c3-9b34e00ba48e', $responseInstance->getTransactionId());

        $this->assertInstanceOf(Money::class, $responseInstance->getRefundableMoney());
        $this->assertEquals('EUR', $responseInstance->getRefundableMoney()->getCurrency());
        $this->assertEquals(10, $responseInstance->getRefundableMoney()->getAmount());

        $this->assertInstanceOf(DateTime::class, $responseInstance->getExpiryDatetime());
        $this->assertEquals('2022-12-17T12:22:55+00:00', $responseInstance->getExpiryDatetime()->format(DATE_ATOM));
    }

    public function testEmptyConstructor()
    {
        $responseInstance = new TransactionRefundableDetailsResponse('');
        $this->assertInstanceOf(TransactionRefundableDetailsResponse::class, $responseInstance);
    }
}
