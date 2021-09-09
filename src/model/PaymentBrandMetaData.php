<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model;

/**
 * This class houses custom data that for example is used to pick a bank using issuerId.
 */
final class PaymentBrandMetaData implements \JsonSerializable
{
    private $properties = [];

    /**
     * Use the createFrom method to create this object.
     */
    private function __construct()
    {
    }

    public static function createFrom(array $data): self
    {
        $object = new self();
        foreach ($data as $key => $value) {
            if (!is_string($key) || is_array($value) || is_object($value)) {
                continue;
            }

            $object->properties[$key] = $value;
        }

        return $object;
    }

    public function hasProperties(): bool
    {
        return !empty($this->properties);
    }

    /**
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource
     */
    public function jsonSerialize()
    {
        $json = [];
        foreach ($this->properties as $key => $value) {
            if (null !== $value) {
                $json[$key] = $value;
            }
        }

        return empty($json) ? null : $json;
    }
}
