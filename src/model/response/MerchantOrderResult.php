<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use InvalidArgumentException;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SignatureDataProvider;

/**
 * This object contains information, like status and paid amount, of an order.
 */
class MerchantOrderResult implements SignatureDataProvider
{
    /** @var int */
    private $poiId;
    /** @var string */
    private $merchantOrderId;
    /** @var string */
    private $omnikassaOrderId;
    /** @var string */
    private $orderStatus;
    /** @var string */
    private $orderStatusDateTime;
    /** @var string */
    private $errorCode;
    /** @var Money */
    private $paidAmount;
    /** @var Money */
    private $totalAmount;
    /** @var TransactionInfo[] */
    private $transactionInfo = [];

    /**
     * @return int
     */
    public function getPoiId()
    {
        return $this->poiId;
    }

    /**
     * @param int $poiId
     */
    public function setPoiId($poiId)
    {
        $this->poiId = $poiId;
    }

    /**
     * @return string
     */
    public function getMerchantOrderId()
    {
        return $this->merchantOrderId;
    }

    /**
     * @param string $merchantOrderId
     */
    public function setMerchantOrderId($merchantOrderId)
    {
        $this->merchantOrderId = $merchantOrderId;
    }

    /**
     * @return string
     */
    public function getOmnikassaOrderId()
    {
        return $this->omnikassaOrderId;
    }

    /**
     * @param string $omnikassaOrderId
     */
    public function setOmnikassaOrderId($omnikassaOrderId)
    {
        $this->omnikassaOrderId = $omnikassaOrderId;
    }

    /**
     * @return string
     */
    public function getOrderStatus()
    {
        return $this->orderStatus;
    }

    /**
     * @param string $orderStatus
     */
    public function setOrderStatus($orderStatus)
    {
        $this->orderStatus = $orderStatus;
    }

    /**
     * @return string
     */
    public function getOrderStatusDateTime()
    {
        return $this->orderStatusDateTime;
    }

    /**
     * @param string $orderStatusDateTime
     */
    public function setOrderStatusDateTime($orderStatusDateTime)
    {
        $this->orderStatusDateTime = $orderStatusDateTime;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param string $errorCode
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
    }

    /**
     * @return Money
     */
    public function getPaidAmount()
    {
        return $this->paidAmount;
    }

    public function setPaidAmount(Money $paidAmount)
    {
        $this->paidAmount = $paidAmount;
    }

    /**
     * @return Money
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(Money $totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return TransactionInfo[]
     */
    public function getTransactionInfo()
    {
        return $this->transactionInfo;
    }

    /**
     * @param TransactionInfo[] $transactionInfo
     *
     * @internal for testing only
     *
     * @deprecated for testing only
     */
    public function setTransactionInfo(array $transactionInfo)
    {
        $this->transactionInfo = $transactionInfo;
    }

    /**
     * @param array $transactions
     */
    public function setTransactions($transactions)
    {
        if (!is_array($transactions)) {
            return;
        }

        $this->transactionInfo = [];

        foreach ($transactions as $transaction) {
            $this->transactionInfo[] = new TransactionInfo($transaction);
        }
    }

    public function getSignatureData()
    {
        $data = [
            $this->merchantOrderId,
            $this->omnikassaOrderId,
            $this->poiId,
            $this->orderStatus,
            $this->orderStatusDateTime,
            $this->errorCode,
            $this->paidAmount->getSignatureData(),
            $this->totalAmount->getSignatureData(),
        ];

        foreach ($this->getTransactionInfo() as $current) {
            $data[] = $current->getSignatureData();
        }

        return $data;
    }

    /**
     * @param $data \stdClass
     */
    public static function createFromJsonData($data): self
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Data expected but missing');
        }

        $instance = new self();

        if (!empty($data->poiId)) {
            // Warning: field differs from internal name
            $instance->poiId = (int) $data->poiId;
        }

        if (!empty($data->merchantOrderId)) {
            $instance->merchantOrderId = $data->merchantOrderId;
        }

        if (!empty($data->omnikassaOrderId)) {
            $instance->omnikassaOrderId = $data->omnikassaOrderId;
        }

        if (!empty($data->orderStatus)) {
            $instance->orderStatus = $data->orderStatus;
        }

        if (!empty($data->orderStatusDateTime)) {
            $instance->orderStatusDateTime = $data->orderStatusDateTime;
        }

        if (!empty($data->errorCode)) {
            $instance->errorCode = $data->errorCode;
        }

        if (!empty($data->paidAmount)) {
            $instance->paidAmount = Money::fromCents($data->paidAmount->currency, $data->paidAmount->amount);
        }

        if (!empty($data->totalAmount)) {
            $instance->totalAmount = Money::fromCents($data->totalAmount->currency, $data->totalAmount->amount);
        }

        if (!empty($data->transactions) && is_array($data->transactions)) {
            foreach ($data->transactions as $current) {
                $instance->transactionInfo[] = new TransactionInfo($current);
            }
        }

        return $instance;
    }
}
