<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\response;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\OrderDetails;

final class OrderDetailsBuilder
{
    public static function newInstance(): OrderDetails
    {
        return new OrderDetails(json_encode(self::getData()));
    }

    public static function newInstanceAsJson(): string
    {
        return json_encode(self::getData());
    }

    private static function getData(): array
    {
        return [
            'order' => [
                'id' => '1d0a95f4-2589-439b-9562-c50aa19f9caf',
                'merchantOrderId' => 'merchant456',
                'status' => 'COMPLETED',
                'totalAmount' => [
                    'currency' => 'EUR',
                    'amount' => 2500,
                ],
                'transactions' => [
                    [
                        'id' => 'txn789',
                        'paymentBrand' => 'IDEAL',
                        'type' => 'PAYMENT',
                        'status' => 'SUCCESS',
                        'amount' => [
                            'currency' => 'EUR',
                            'amount' => 2500,
                        ],
                        'createdAt' => '2025-06-30T10:00:00Z',
                        'lastUpdatedAt' => '2025-06-30T11:00:00Z',
                    ],
                ],
            ],
        ];
    }
}
