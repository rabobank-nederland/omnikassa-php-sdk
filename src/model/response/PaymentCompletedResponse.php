<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use InvalidArgumentException;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\InvalidSignatureException;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SignedResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SigningKey;

class PaymentCompletedResponse extends SignedResponse
{
    /** @var string */
    protected $orderID;
    /** @var string */
    protected $status;

    /**
     * @param string $orderID
     * @param string $status
     * @param string $signature
     */
    private function __construct($orderID, $status, $signature)
    {
        $this->orderID = $orderID;
        $this->status = $status;
        $this->setSignature($signature);
    }

    /**
     * @return string
     */
    public function getOrderID()
    {
        return $this->orderID;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Creates a new PaymentCompletedResponse instance.
     * It sanitizes the input and validates the signature resulting in a valid instance or the value FALSE.
     *
     * @param string $orderID
     * @param string $status
     * @param string $signature
     *
     * @return bool|PaymentCompletedResponse returns FALSE if the signature is invalid, otherwise the instance is returned
     */
    public static function createInstance($orderID, $status, $signature, SigningKey $signingKey)
    {
        // Sanitize input
        $sanitizedOrderID = preg_replace('/[^0-9A-Za-z]/', '', $orderID);
        $sanitizedStatus = preg_replace('/[^A-Z_]/', '', $status);
        $sanitizedSignature = preg_replace('/[^0-9a-f]/', '', $signature);

        if ($sanitizedOrderID !== $orderID || $sanitizedStatus !== $status || $sanitizedSignature !== $signature) {
            throw new InvalidArgumentException('One or more parameters in the merchantReturnUrl did not match the required format.');
        }

        $instance = new PaymentCompletedResponse($orderID, $status, $signature);
        try {
            $instance->validateSignature($signingKey);
        } catch (InvalidSignatureException $invalidSignatureException) {
            return false;
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function getSignatureData()
    {
        return [$this->orderID, $this->status];
    }
}
