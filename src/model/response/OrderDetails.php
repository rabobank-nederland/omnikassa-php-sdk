<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Transaction;

/**
 * Represents the details of an order.
 */
final class OrderDetails
{
    public string $id;
    public string $merchantOrderId;
    public string $status;
    public Money $totalAmount;
    /** @var list<Transaction> */
    public array $transactions = [];

    public function __construct(string $json)
    {
        if (empty($json)) {
            return;
        }

        $data = json_decode($json, true, JSON_THROW_ON_ERROR);

        $orderData = $data['order'] ?? [];

        $this->id = $orderData['id'];
        $this->merchantOrderId = $orderData['merchantOrderId'];
        $this->status = $orderData['status'];

        $this->totalAmount = Money::fromCents(
            $orderData['totalAmount']['currency'] ?? 'EUR',
            $orderData['totalAmount']['amount'] ?? 0
        );

        $this->transactions = array_map(function ($transactionData) {
            return Transaction::createFrom($transactionData);
        }, $orderData['transactions']);
    }
}
