<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model;

use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testFromCents()
    {
        $this->assertAmountFromCents(100);
        $this->assertAmountFromCents(100.00);
        $this->assertAmountFromCents(100.00001);
    }

    public function testFromDecimal()
    {
        $this->assertMoneyFromDecimal(500, 5.00);
        $this->assertMoneyFromDecimal(999, 9.99);
        $this->assertMoneyFromDecimal(1000, 9.999);
        $this->assertMoneyFromDecimal(999, 9.991);
        $this->assertMoneyFromDecimal(1000, 9.995);
        $this->assertMoneyFromDecimal(3791, 37.91);
        $this->assertMoneyFromDecimal(6857, 68.57);
    }

    public function testSignature()
    {
        $expectedSignatureData = [
            'EUR',
            100,
        ];
        $money = Money::fromCents('EUR', 100);
        $actualSignatureData = $money->getSignatureData();

        $this->assertEquals($expectedSignatureData, $actualSignatureData);
    }

    public function testJsonSerialize()
    {
        $expectedJson = [
            'currency' => 'EUR',
            'amount' => 100,
        ];
        $money = Money::fromCents('EUR', 100);
        $actualJson = $money->jsonSerialize();

        $this->assertEquals($expectedJson, $actualJson);
    }

    private function assertAmountFromCents($amount)
    {
        $money = Money::fromCents('EUR', $amount);

        $this->assertEquals('EUR', $money->getCurrency());
        $this->assertEquals(intval($amount), $money->getAmount());
    }

    private function assertMoneyFromDecimal($expected, $amountInDecimal)
    {
        $money = Money::fromDecimal('EUR', $amountInDecimal);
        $this->assertEquals($expected, $money->getAmount());
        $this->assertEquals('EUR', $money->getCurrency());
    }
}
