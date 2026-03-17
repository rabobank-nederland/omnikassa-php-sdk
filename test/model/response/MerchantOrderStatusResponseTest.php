<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use ErrorException;
use JsonMapper_Exception;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\TransactionInfo;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response\MerchantOrderStatusResponseBuilder;
use PHPUnit\Framework\TestCase;

class MerchantOrderStatusResponseTest extends TestCase
{
    public function testThatObjectIsCorrectlyConstructed()
    {
        $merchantOrderResult = new MerchantOrderResult();
        $merchantOrderResult->setPoiId(1000);
        $merchantOrderResult->setMerchantOrderId('10');
        $merchantOrderResult->setOmnikassaOrderId('1');
        $merchantOrderResult->setOrderStatus('CANCELLED');
        $merchantOrderResult->setErrorCode('666');
        $merchantOrderResult->setOrderStatusDateTime('1970-01-01T00:00:00.000+02:00');
        $merchantOrderResult->setPaidAmount(Money::fromDecimal('EUR', 1));
        $merchantOrderResult->setTotalAmount(Money::fromDecimal('EUR', 1));
        $merchantOrderResult->setTransactionInfo([
            new TransactionInfo((object) [
                'id' => '791fdcda-65e4-4f81-8b45-988404da3a8d',
                'paymentBrand' => 'IDEAL',
                'type' => 'PAYMENT',
                'status' => 'SUCCESS',
                'amount' => (object) ['currency' => 'EUR', 'amount' => 100],
                'confirmedAmount' => (object) ['currency' => 'EUR', 'amount' => 100],
                'startTime' => '2026-02-17T11:15:29.244+01:00',
                'lastUpdateTime' => '2026-02-17T11:15:32.698+01:00',
            ]),
        ]);

        $expectedOrderResults = [
            $merchantOrderResult,
        ];

        $response = MerchantOrderStatusResponseBuilder::newInstance();

        $this->assertTrue($response->isMoreOrderResultsAvailable());
        $this->assertEquals($expectedOrderResults, $response->getOrderResults());

        $transactionInfo = $response->getOrderResults()[0]->getTransactionInfo();
        $this->assertCount(1, $transactionInfo);
        $this->assertEquals('791fdcda-65e4-4f81-8b45-988404da3a8d', $transactionInfo[0]->getId());
        $this->assertEquals('IDEAL', $transactionInfo[0]->getPaymentBrand());
        $this->assertEquals('PAYMENT', $transactionInfo[0]->getType());
        $this->assertEquals('SUCCESS', $transactionInfo[0]->getStatus());
        $this->assertEquals(Money::fromDecimal('EUR', 1), $transactionInfo[0]->getAmount());
        $this->assertEquals(Money::fromDecimal('EUR', 1), $transactionInfo[0]->getConfirmedAmount());
        $this->assertEquals('2026-02-17T11:15:29.244+01:00', $transactionInfo[0]->getStartTime());
        $this->assertEquals('2026-02-17T11:15:32.698+01:00', $transactionInfo[0]->getLastUpdateTime());
    }

    /**
     * @throws JsonMapper_Exception
     */
    public function testThatInvalidSignatureExceptionIsThrownWhenTheSignaturesDoNotMatch()
    {
        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage('The signature validation of the response failed. Please contact the Rabobank service team.');

        MerchantOrderStatusResponseBuilder::invalidSignatureInstance();
    }
}
