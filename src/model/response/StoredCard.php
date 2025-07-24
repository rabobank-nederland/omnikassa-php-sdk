<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use JsonSerializable;

/**
 * Represents the details of a shoppers' stored card.
 */
final class StoredCard implements JsonSerializable
{
    private string $reference;
    private string $last4Digits;
    private string $brand;
    private string $cardExpiry;
    private string $tokenExpiry;
    private string $status;

    public function __construct(array $data)
    {
        $this->reference = $data['reference'];
        $this->last4Digits = $data['last4Digits'];
        $this->brand = $data['brand'];
        $this->cardExpiry = $data['cardExpiry'];
        $this->tokenExpiry = $data['tokenExpiry'];
        $this->status = $data['status'];
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getLast4Digits(): string
    {
        return $this->last4Digits;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function getCardExpiry(): string
    {
        return $this->cardExpiry;
    }

    public function getTokenExpiry(): string
    {
        return $this->tokenExpiry;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return array<string,string>
     */
    public function jsonSerialize(): array
    {
        return [
            'reference' => $this->reference,
            'last4Digits' => $this->last4Digits,
            'brand' => $this->brand,
            'cardExpiry' => $this->cardExpiry,
            'tokenExpiry' => $this->tokenExpiry,
            'status' => $this->status,
        ];
    }
}
