<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use JsonSerializable;

/**
 * This class contains a list of IDEALIssuersInfos.
 */
class IdealIssuersResponse implements JsonSerializable
{
    /** @var IdealIssuersInfo[] */
    protected $issuers;

    /**
     * Construct this response from the given json.
     */
    public function __construct(string $json)
    {
        if (empty($json)) {
            return;
        }

        foreach (json_decode($json)->issuers as $index => $value) {
            $this->issuers[$index] = new IdealIssuersInfo($value);
        }
    }

    /**
     * @return IdealIssuersInfo[]
     */
    public function getIssuers(): array
    {
        return $this->issuers;
    }

    public function jsonSerialize(): array
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
