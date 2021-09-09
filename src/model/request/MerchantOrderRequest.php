<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request;

/**
 * Envelope for the MerchantOrder.
 */
class MerchantOrderRequest implements \JsonSerializable
{
    /** @var MerchantOrder */
    private $merchantOrder;
    /** @var \DateTime */
    private $timestamp;

    public function __construct(MerchantOrder $merchantOrder)
    {
        $this->merchantOrder = $merchantOrder;
        $this->timestamp = new \DateTime('now');
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        $json['timestamp'] = $this->getFormattedTimestamp();
        foreach ($this->merchantOrder->jsonSerialize() as $key => $value) {
            if (null !== $value) {
                $json[$key] = $value;
            }
        }

        return $json;
    }

    /**
     * @return string
     */
    private function getFormattedTimestamp()
    {
        return $this->timestamp->format(\DateTime::ATOM);
    }

    /**
     * This method should only be called from the tests.
     */
    public function setTimestamp(\DateTime $timestamp)
    {
        $this->timestamp = $timestamp;
    }
}
