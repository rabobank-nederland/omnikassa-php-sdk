<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use DateTime;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;

/**
 * Returned when a refund is created.
 */
class RefundDetailsResponse
{
    /** @var string UUID */
    private $refundId;
    /** @var string|null UUID */
    private $refundTransactionId;
    /** @var DateTime */
    private $createdAt;
    /** @var DateTime|null */
    private $updatedAt;
    /** @var Money */
    private $refundMoney;
    /** @var string|null */
    private $vatCategory;
    /** @var string */
    private $paymentBrand;
    /** @var string */
    private $status;
    /** @var string */
    private $description;
    /** @var string UUID */
    private $transactionId;

    /**
     * Construct this response from the given json.
     *
     * @param string $json
     */
    public function __construct($json)
    {
        if (empty($json)) {
            return;
        }

        $data = json_decode($json);

        if (!empty($data->refundId)) {
            $this->refundId = $data->refundId;
        }

        if (!empty($data->refundTransactionId)) {  // Nullable
            $this->refundTransactionId = $data->refundTransactionId;
        }

        if (!empty($data->createdAt)) {
            $this->createdAt = new DateTime($data->createdAt);
        }

        if (!empty($data->updatedAt)) {  // Nullable
            $this->updatedAt = new DateTime($data->updatedAt);
        }

        if (!empty($data->refundMoney)) {
            $this->refundMoney = Money::fromCents(
                $data->refundMoney->currency,
                $data->refundMoney->amount
            );
        }

        if (!empty($data->vatCategory)) {  // Nullable due to one of the enum values
            $this->vatCategory = $data->vatCategory;
        }

        if (!empty($data->paymentBrand)) {
            $this->paymentBrand = $data->paymentBrand;
        }

        if (!empty($data->status)) {
            $this->status = $data->status;
        }

        if (!empty($data->description)) {
            $this->description = $data->description;
        }

        if (!empty($data->transactionId)) {
            $this->transactionId = $data->transactionId;
        }
    }

    /**
     * Unique ID of this refund.
     *
     * @return string UUID
     */
    public function getRefundId(): string
    {
        return $this->refundId;
    }

    /**
     * @return string|null UUID
     */
    public function getRefundTransactionId(): ?string
    {
        return $this->refundTransactionId;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function getRefundMoney(): Money
    {
        return $this->refundMoney;
    }

    /**
     * VAT applicable. One of:
     * - "HIGH"
     * - "LOW"
     * - "ZERO"
     * - null (= None)
     * .
     */
    public function getVatCategory(): ?string
    {
        return $this->vatCategory;
    }

    /**
     * One of:
     * - "IDEAL"
     * - "IDEAL_QR"
     * - "AFTERPAY"
     * - "VISA"
     * - "MASTERCARD"
     * - "V_PAY"
     * - "MAESTRO"
     * - "SOFORT"
     * .
     */
    public function getPaymentBrand(): string
    {
        return $this->paymentBrand;
    }

    /**
     * One of:
     * - "PENDING"
     * - "SUCCEEDED"
     * - "FAILED"
     * - "UNKNOWN"
     * .
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string The description as given in the refund request
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string UUID
     */
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }
}
