<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\RefundDetailsResponse;

class RefundDetailsResponseBuilder
{
    public static function newInstance(): RefundDetailsResponse
    {
        return new RefundDetailsResponse(json_encode(self::getTestData()));
    }

    public static function newInstanceAsJson(): string
    {
        return json_encode(self::getTestData());
    }

    public static function getTestData(): array
    {
        return [
            'refundId' => '6fa74559-b95d-4d40-9fa9-e866e3c8e2d2',
            'refundTransactionId' => '1e9bf154-e128-42a1-be8d-10f0174b4c3d',  // Nullable
            'createdAt' => '2022-06-20T12:37:37Z',
            'updatedAt' => '2022-01-15T12:34:56Z',  // Nullable
            'vatCategory' => 'LOW',
            'paymentBrand' => 'MASTERCARD',
            'status' => 'PENDING',
            'description' => 'Dit is een test',
            'transactionId' => 'da1e7696-b199-4c87-83c3-9b34e00ba48e',
            'refundMoney' => [
                'currency' => 'EUR',
                'amount' => 10,
            ],
        ];
    }
}
