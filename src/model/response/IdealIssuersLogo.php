<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use JsonSerializable;

/**
 * This class contains the iDEAL issuers logo details.
 */
class IdealIssuersLogo implements JsonSerializable
{
    /** @var string */
    protected $url;
    /** @var string */
    protected $mimeType;

    /**
     * Construct this response from the given json.
     */
    public function __construct($data)
    {
        if (empty($data)) {
            return;
        }

        if (!empty($data->url)) {
            $this->url = $data->url;
        }

        if (!empty($data->mimeType)) {
            $this->mimeType = $data->mimeType;
        }
    }

    /**
     * @return array
     */
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

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }
}
