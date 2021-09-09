<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use JsonSerializable;

/**
 * This class contains a list of PaymentBrandInfos.
 */
class PaymentBrandsResponse implements JsonSerializable
{
    /** @var PaymentBrandInfo[] */
    protected $paymentBrands;

    /**
     * Construct this response from the given json.
     *
     * @param string $json
     *
     * @throws \JsonMapper_Exception
     */
    public function __construct($json)
    {
        if (empty($json)) {
            return;
        }

        foreach (json_decode($json)->paymentBrands as $index => $value) {
            $this->paymentBrands[$index] = new PaymentBrandInfo($value);
        }
    }

    /**
     * @param PaymentBrandInfo[] $paymentBrands
     */
    public function setPaymentBrands($paymentBrands)
    {
        $this->paymentBrands = $paymentBrands;
    }

    /**
     * @return PaymentBrandInfo[]
     */
    public function getPaymentBrands()
    {
        return $this->paymentBrands;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $json = [];
        foreach ($this as $key => $value) {
            if (null !== $value) {
                $json[$key] = $value;
            }
        }

        return $json;
    }
}
