<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model;

use DateTimeImmutable;
use InvalidArgumentException;

final class Transaction
{
    private string $id;
    private string $paymentBrand;
    private string $type;
    private string $status;
    private Money $amount;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $lastUpdatedAt;

    private function __construct()
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPaymentBrand(): string
    {
        return $this->paymentBrand;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getLastUpdatedAt(): DateTimeImmutable
    {
        return $this->lastUpdatedAt;
    }

    /**
     * Create a Transaction from array data.
     */
    public static function createFrom(array $data): self
    {
        $typedProperties = [
            'amount' => Money::fromCents($data['amount']['currency'], $data['amount']['amount']),
            'createdAt' => new DateTimeImmutable($data['createdAt']),
            'lastUpdatedAt' => new DateTimeImmutable($data['lastUpdatedAt']),
        ];

        $transaction = new self();
        foreach ($data as $key => $value) {
            if (false === property_exists($transaction, $key)) {
                $properties = implode(', ', array_keys(get_object_vars($transaction)));
                throw new InvalidArgumentException("Invalid property {$key} supplied. Valid properties for Transaction are: {$properties}");
            }

            if (array_key_exists($key, $typedProperties)) {
                $transaction->$key = $typedProperties[$key];
                continue;
            }

            $transaction->$key = $value;
        }

        return $transaction;
    }
}
