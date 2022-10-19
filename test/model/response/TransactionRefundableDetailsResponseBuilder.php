<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\TransactionRefundableDetailsResponse;

class TransactionRefundableDetailsResponseBuilder
{
    public static function newInstance(): TransactionRefundableDetailsResponse
    {
        return new TransactionRefundableDetailsResponse(json_encode(self::getTestData()));
    }

    public static function newInstanceAsJson(): string
    {
        return json_encode(self::getTestData());
    }

    private static function getTestData(): array
    {
        return [
            'transactionId' => 'da1e7696-b199-4c87-83c3-9b34e00ba48e',
            'refundableMoney' => [
                'currency' => 'EUR',
                'amount' => 10,
            ],
            'expiryDatetime' => '2022-12-17T12:22:55Z',
        ];
    }
}
