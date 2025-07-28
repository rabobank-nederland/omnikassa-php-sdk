<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use JsonSerializable;

/**
 * This class contains the iDEAL issuers details.
 */
class IdealIssuersInfo implements JsonSerializable
{
    /** @var string */
    protected $id;
    /** @var string */
    protected $name;
    /** @var array */
    protected $logos = [];
    /** @var string */
    protected $countryNames;

    /**
     * Construct this response from the given json.
     */
    public function __construct($data)
    {
        if (empty($data)) {
            return;
        }

        if (!empty($data->id)) {
            $this->id = $data->id;
        }

        if (!empty($data->name)) {
            $this->name = $data->name;
        }

        if (!empty($data->countryNames)) {
            $this->countryNames = $data->countryNames;
        }

        if (!empty($data->logos) && is_array($data->logos)) {
            foreach ($data->logos as $logo) {
                $this->logos[] = new IdealIssuersLogo($logo);
            }
        }
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

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return IdealIssuersLogo[]
     */
    public function getLogos(): array
    {
        return $this->logos;
    }

    public function getCountryNames(): string
    {
        return $this->countryNames;
    }
}
