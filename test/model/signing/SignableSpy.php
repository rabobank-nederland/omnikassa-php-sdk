<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\signing;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\Signable;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SigningKey;

class SignableSpy extends Signable
{
    /**
     * @var array;
     */
    private $signatureData;

    /**
     * SignableSpy constructor.
     */
    public function __construct(array $signatureData)
    {
        $this->signatureData = $signatureData;
    }

    public function getCalculatedSignature(SigningKey $signingKey)
    {
        return $this->calculateSignature($signingKey);
    }

    /**
     * @return array
     */
    public function getSignatureData()
    {
        return $this->signatureData;
    }
}
