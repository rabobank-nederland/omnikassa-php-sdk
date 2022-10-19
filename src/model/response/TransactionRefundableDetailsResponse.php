<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;

class TransactionRefundableDetailsResponse
{
    /** @var string UUID */
    protected $transactionId;
    /** @var Money */
    private $refundableMoney;
    /** @var \DateTime */
    private $expiryDatetime;

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

        if (isset($data->transactionId)) {
            $this->transactionId = $data->transactionId;
        }

        if (isset($data->refundableMoney)) {
            $this->refundableMoney = Money::fromCents(
                $data->refundableMoney->currency,
                $data->refundableMoney->amount
            );
        }

        if (isset($data->expiryDatetime)) {
            $this->expiryDatetime = new \DateTime($data->expiryDatetime);
        }
    }

    /**
     * @return string UUID
     */
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getRefundableMoney(): Money
    {
        return $this->refundableMoney;
    }

    /**
     * When expired, a refund is no longer possible.
     */
    public function getExpiryDatetime(): \DateTime
    {
        return $this->expiryDatetime;
    }
}
