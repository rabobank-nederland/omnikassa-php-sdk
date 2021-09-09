<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use JsonSerializable;

/**
 * this class contains the name and status of a payment brand.
 */
class PaymentBrandInfo implements JsonSerializable
{
    /** @var string */
    protected $name;
    /** @var bool */
    protected $active;

    /**
     * Construct this response from the given json.
     *
     * @param array $data
     *
     * @throws \JsonMapper_Exception
     */
    public function __construct($data)
    {
        if (empty($data)) {
            return;
        }

        if (!empty($data->name)) {
            $this->name = $data->name;
        }
        if (!empty($data->status)) {
            $this->setActive($data->status);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    private function setActive($status)
    {
        if ('Active' === $status) {
            $this->active = true;
        } else {
            $this->active = false;
        }
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
