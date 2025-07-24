<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use ErrorException;
use JsonMapper_Exception;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
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

        $expectedOrderResults = [
            $merchantOrderResult,
        ];

        $response = MerchantOrderStatusResponseBuilder::newInstance();

        $this->assertTrue($response->isMoreOrderResultsAvailable());
        $this->assertEquals($expectedOrderResults, $response->getOrderResults());
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
