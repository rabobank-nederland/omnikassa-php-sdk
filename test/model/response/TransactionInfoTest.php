<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use InvalidArgumentException;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use PHPUnit\Framework\TestCase;

class TransactionInfoTest extends TestCase
{
    public function testThatObjectIsCorrectlyConstructed()
    {
        $instance = new TransactionInfo(
            json_decode(json_encode(
                [
                    'id' => '22b36073-57a3-4c3d-9585-87f2e55275a5',
                    'paymentBrand' => 'IDEAL',
                    'type' => 'AUTHORIZE',
                    'status' => 'SUCCESS',
                    'amount' => [
                        'amount' => 123,
                        'currency' => 'EUR',
                    ],
                    'confirmedAmount' => [
                        'amount' => 456,
                        'currency' => 'EUR',
                    ],
                    'startTime' => '2018-03-20T09:12:28Z',
                    'lastUpdateTime' => '2022-03-20T09:12:28Z',
                ]
            ))
        );

        $this->assertEquals('22b36073-57a3-4c3d-9585-87f2e55275a5', $instance->getId());
        $this->assertEquals('IDEAL', $instance->getPaymentBrand());
        $this->assertEquals('AUTHORIZE', $instance->getType());
        $this->assertEquals('SUCCESS', $instance->getStatus());
        $this->assertEquals(Money::fromCents('EUR', 123), $instance->getAmount());
        $this->assertEquals(Money::fromCents('EUR', 456), $instance->getConfirmedAmount());
        $this->assertEquals('2018-03-20T09:12:28Z', $instance->getStartTime());
        $this->assertEquals('2022-03-20T09:12:28Z', $instance->getLastUpdateTime());
    }

    public function testInvalidInstantiate()
    {
        $this->expectException(InvalidArgumentException::class);
        new TransactionInfo(null);
    }
}
