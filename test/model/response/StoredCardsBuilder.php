<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\StoredCard;

final class StoredCardsBuilder
{
    public static function newInstance(): array
    {
        return self::getData();
    }

    public static function newInstanceAsJson(): string
    {
        return json_encode(self::getData());
    }

    private static function getData(): array
    {
        return [
            new StoredCard([
                'reference' => '12345677',
                'last4Digits' => '9853',
                'brand' => 'MAESTRO',
                'cardExpiry' => '3261-78',
                'tokenExpiry' => '4570-45',
                'status' => 'ACTIVE',
            ]),
            new StoredCard([
                'reference' => '12345688',
                'last4Digits' => '9800',
                'brand' => 'MAESTRO',
                'cardExpiry' => '3261-00',
                'tokenExpiry' => '4570-11',
                'status' => 'ACTIVE',
            ]),
        ];
    }
}
