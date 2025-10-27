<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use DateTimeImmutable;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Transaction;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\OrderDetailsBuilder;
use PHPUnit\Framework\TestCase;

final class OrderDetailsTest extends TestCase
{
    public function testThatObjectIsCorrectlyConstructed()
    {
        $response = OrderDetailsBuilder::newInstance();

        $this->assertEquals('1d0a95f4-2589-439b-9562-c50aa19f9caf', $response->id);
        $this->assertEquals('merchant456', $response->merchantOrderId);
        $this->assertEquals('COMPLETED', $response->status);

        $this->assertInstanceOf(Money::class, $response->totalAmount);
        $this->assertEquals('EUR', $response->totalAmount->getCurrency());
        $this->assertEquals(2500, $response->totalAmount->getAmount());

        $this->assertIsArray($response->transactions);
        $this->assertCount(1, $response->transactions);
        $this->assertInstanceOf(Transaction::class, $response->transactions[0]);
        $this->assertInstanceOf(DateTimeImmutable::class, $response->transactions[0]->getCreatedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $response->transactions[0]->getLastUpdatedAt());
        $this->assertEquals('2025-06-30T10:00:00+00:00', $response->transactions[0]->getCreatedAt()->format('c'));
    }
}
