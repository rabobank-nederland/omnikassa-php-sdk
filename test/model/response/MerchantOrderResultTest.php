<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use PHPUnit\Framework\TestCase;

class MerchantOrderResultTest extends TestCase
{
    public function testObjectMethods()
    {
        $merchantOrderResult = new MerchantOrderResult();
        $merchantOrderResult->setPoiId(2004);
        $merchantOrderResult->setMerchantOrderId('order123');
        $merchantOrderResult->setOmnikassaOrderId('1d0a95f4-2589-439b-9562-c50aa19f9caf');
        $merchantOrderResult->setOrderStatus('COMPLETED');
        $merchantOrderResult->setErrorCode('sigriulhocekme');
        $merchantOrderResult->setOrderStatusDateTime('2018-11-25T12:20:03.157+00:00');
        $merchantOrderResult->setPaidAmount(Money::fromDecimal('EUR', 109.96));
        $merchantOrderResult->setTotalAmount(Money::fromDecimal('EUR', 109.97));

        $this->assertEquals(2004, $merchantOrderResult->getPoiId());
        $this->assertEquals('order123', $merchantOrderResult->getMerchantOrderId());
        $this->assertEquals('1d0a95f4-2589-439b-9562-c50aa19f9caf', $merchantOrderResult->getOmnikassaOrderId());
        $this->assertEquals('COMPLETED', $merchantOrderResult->getOrderStatus());
        $this->assertEquals('sigriulhocekme', $merchantOrderResult->getErrorCode());
        $this->assertEquals('2018-11-25T12:20:03.157+00:00', $merchantOrderResult->getOrderStatusDateTime());
        $this->assertEquals(Money::fromDecimal('EUR', 109.96), $merchantOrderResult->getPaidAmount());
        $this->assertEquals(Money::fromDecimal('EUR', 109.97), $merchantOrderResult->getTotalAmount());
    }

    /**
     * Verify signature field setup for nested structure.
     */
    public function testNestedSignature()
    {
        $json = '{"merchantOrderId":"order123","omnikassaOrderId":"1d0a95f4-2589-439b-9562-c50aa19f9caf","poiId":"2004","orderStatus":"COMPLETED","orderStatusDateTime":"2018-11-25T12:20:03.157+00:00","errorCode":"sigriulhocekme","paidAmount":{"amount":10997,"currency":"EUR"},"totalAmount":{"amount":10997,"currency":"EUR"},"transactions":[{"id":"22b36073-57a3-4c3d-9585-87f2e55275a5","paymentBrand":"IDEAL","type":"AUTHORIZE","status":"SUCCESS","amount":{"amount":10997,"currency":"EUR"},"confirmedAmount":{"amount":10997,"currency":"EUR"},"startTime":"2018-03-20T09:12:28Z","lastUpdateTime":"2018-03-20T09:12:28Z"}]}';
        $instance = MerchantOrderResult::createFromJsonData(json_decode($json));
        $this->assertInstanceOf(MerchantOrderResult::class, $instance);

        /**
         * @see Signable::flattenAndJoin()
         */
        $signatureData = $instance->getSignatureData();
        $signatureData = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($signatureData)), false);
        $signatureData = implode(',', $signatureData);

        $expectedData = 'order123,1d0a95f4-2589-439b-9562-c50aa19f9caf,2004,COMPLETED,2018-11-25T12:20:03.157+00:00,sigriulhocekme,EUR,10997,EUR,10997,22b36073-57a3-4c3d-9585-87f2e55275a5,IDEAL,AUTHORIZE,SUCCESS,EUR,10997,EUR,10997,2018-03-20T09:12:28Z,2018-03-20T09:12:28Z';
        $this->assertEquals($expectedData, $signatureData);
    }

    /**
     * Mirrors Java SDK.
     *
     * @see https://github.com/rabobank-nederland/omnikassa-java-sdk/blob/master/sdk/src/test/java/nl/rabobank/gict/payments_savings/omnikassa_frontend/sdk/model/response/MerchantOrderResultTest.java
     */
    public function testSignature()
    {
        $instance = new MerchantOrderResult();
        $instance->setPoiId(1);
        $instance->setMerchantOrderId('SHOP1');
        $instance->setOmnikassaOrderId('ORDER1');
        $instance->setOrderStatus('COMPLETED');
        $instance->setErrorCode('NONE');
        $instance->setOrderStatusDateTime('2000-01-01T00:00:00.000-0200');
        $instance->setPaidAmount(Money::fromCents('EUR', 100));
        $instance->setTotalAmount(Money::fromCents('EUR', 100));
        $instance->setTransactionInfo([
            $this->getTransactionObject('1', 100, true),
            $this->getTransactionObject('2', 200, true),
            $this->getTransactionObject('3', 300, false),
        ]);

        /**
         * @see Signable::flattenAndJoin()
         */
        $signatureData = $instance->getSignatureData();
        $signatureData = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($signatureData)), false);
        $signatureData = implode(',', $signatureData);

        $expectedData = 'SHOP1,ORDER1,1,COMPLETED,2000-01-01T00:00:00.000-0200,NONE,EUR,100,EUR,100,1,IDEAL,PAYMENT,SUCCESS,EUR,100,EUR,100,2016-07-28T12:51:15.574+02:00,2016-07-28T12:51:15.574+02:00,2,IDEAL,PAYMENT,SUCCESS,EUR,200,EUR,200,2016-07-28T12:51:15.574+02:00,2016-07-28T12:51:15.574+02:00,3,IDEAL,PAYMENT,SUCCESS,EUR,300,,,2016-07-28T12:51:15.574+02:00,2016-07-28T12:51:15.574+02:00';
        $this->assertEquals($expectedData, $signatureData);
    }

    /**
     * @see https://github.com/rabobank-nederland/omnikassa-java-sdk/blob/master/sdk/src/test/java/nl/rabobank/gict/payments_savings/omnikassa_frontend/sdk/model/response/MerchantOrderResultTest.java
     */
    private function getTransactionObject(string $id, int $amount, bool $withConfirmedAmount): TransactionInfo
    {
        return new TransactionInfo(
            json_decode(json_encode(
                [
                    'id' => $id,
                    'paymentBrand' => 'IDEAL',
                    'type' => 'PAYMENT',
                    'status' => 'SUCCESS',
                    'amount' => [
                        'amount' => $amount,
                        'currency' => 'EUR',
                    ],
                    'confirmedAmount' => $withConfirmedAmount ? [
                        'amount' => $amount,
                        'currency' => 'EUR',
                    ] : null,
                    'startTime' => '2016-07-28T12:51:15.574+02:00',
                    'lastUpdateTime' => '2016-07-28T12:51:15.574+02:00',
                ]
            ))
        );
    }

    /**
     * Checks cross compatibility.
     *
     * @deprecated support these until further notice
     */
    public function testDeprecations()
    {
        $merchantOrderResult = new MerchantOrderResult();

        $merchantOrderResult->setPoiId(12345);
        $this->assertEquals(12345, $merchantOrderResult->getPoiId());

        $merchantOrderResult->setPoiId(67890);
        $this->assertEquals(67890, $merchantOrderResult->getPoiId());
    }
}
