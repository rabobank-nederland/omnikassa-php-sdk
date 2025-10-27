<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use InvalidArgumentException;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SignatureDataProvider;
use stdClass;

class TransactionInfo implements SignatureDataProvider
{
    /** @var string UUID */
    private $id;
    /** @var string */
    private $paymentBrand;
    /** @var string */
    private $type;
    /** @var string */
    private $status;
    /** @var Money */
    private $amount;
    /** @var Money|null */
    private $confirmedAmount;
    /** @var string (String due to signature validation) */
    private $startTime;
    /** @var string (String due to signature validation) */
    private $lastUpdateTime;

    /**
     * Construct this response from the given json.
     *
     * @param stdClass $data
     */
    public function __construct($data)
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Data expected but missing');
        }

        if (!empty($data->id)) {
            $this->id = $data->id;
        }

        if (!empty($data->paymentBrand)) {
            $this->paymentBrand = $data->paymentBrand;
        }

        if (!empty($data->type)) {
            $this->type = $data->type;
        }

        if (!empty($data->status)) {
            $this->status = $data->status;
        }

        if (!empty($data->amount)) {
            $this->amount = Money::fromCents($data->amount->currency, $data->amount->amount);
        }

        if (!empty($data->confirmedAmount)) {
            $this->confirmedAmount = Money::fromCents($data->confirmedAmount->currency, $data->confirmedAmount->amount);
        }

        if (!empty($data->startTime)) {
            // Note that this is preserved as a string due to signature compatibility.
            $this->startTime = $data->startTime;
        }

        if (!empty($data->lastUpdateTime)) {
            // Note that this is preserved as a string due to signature compatibility.
            $this->lastUpdateTime = $data->lastUpdateTime;
        }
    }

    /**
     * @return string UUID
     */
    public function getId()
    {
        return $this->id;
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
     *
     * @return string
     */
    public function getPaymentBrand()
    {
        return $this->paymentBrand;
    }

    /**
     * One of:
     * - "PAYMENT"
     * - "REFUND"
     * - "AUTHORIZE"
     * - "CAPTURE"
     * .
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * One of:
     * - "SUCCESS"
     * - "CANCELLED"
     * - "EXPIRED"
     * - "FAILURE"
     * - "OPEN"
     * - "NEW"
     * - "ACCEPTED"
     * .
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return Money
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return Money|null
     */
    public function getConfirmedAmount()
    {
        return $this->confirmedAmount;
    }

    /**
     * @return string
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return string
     */
    public function getLastUpdateTime()
    {
        return $this->lastUpdateTime;
    }

    public function getSignatureData()
    {
        return [
            $this->getId(),
            $this->getPaymentBrand(),
            $this->getType(),
            $this->getStatus(),
            $this->getAmount()->getSignatureData(),
            $this->getConfirmedAmount() ? $this->getConfirmedAmount()->getSignatureData() : [null, null],
            $this->getStartTime(),
            $this->getLastUpdateTime(),
        ];
    }
}
