<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model;

class AccessToken implements \JsonSerializable
{
    /** @var string */
    private $token;
    /** @var \DateTime */
    private $validUntil;
    /** @var int */
    private $durationInMillis;

    /**
     * @param string    $token
     * @param \DateTime $validUntil
     * @param int       $durationInMillis
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($token, $validUntil, $durationInMillis)
    {
        if (null === $token) {
            throw new \InvalidArgumentException('Token cannot be empty');
        }
        if (null === $validUntil) {
            throw new \InvalidArgumentException('Valid until cannot be empty');
        }
        if (null === $durationInMillis) {
            throw new \InvalidArgumentException('Duration in milliseconds cannot be empty');
        }
        $this->token = $token;
        $this->validUntil = $validUntil;
        $this->durationInMillis = $durationInMillis;
    }

    /**
     * Construct an access token from the given json.
     *
     * @param string $json
     *
     * @return AccessToken
     *
     * @throws \InvalidArgumentException
     */
    public static function fromJson($json)
    {
        static $requiredKeys = ['token' => true, 'validUntil' => true, 'durationInMillis' => true];

        if (!is_string($json)) {
            throw new \InvalidArgumentException('JSON data must be a string');
        }

        try {
            $result = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \InvalidArgumentException('Invalid JSON data', 0, $e);
        }

        if (!is_array($result) || count(array_intersect_key($result, $requiredKeys)) !== count($requiredKeys)) {
            throw new \InvalidArgumentException(sprintf(
                'JSON data must be an array containing the keys: %s',
                implode(', ', array_keys($requiredKeys))
            ));
        }

        return new AccessToken($result['token'], new \DateTime($result['validUntil']), $result['durationInMillis']);
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return \DateTime
     */
    public function getValidUntil()
    {
        return $this->validUntil;
    }

    /**
     * @return int
     */
    public function getDurationInMillis()
    {
        return $this->durationInMillis;
    }

    /**
     * @return int
     */
    public function getDurationInSeconds()
    {
        return $this->durationInMillis / 1000;
    }

    public function jsonSerialize(): array
    {
        return [
            'token' => $this->token,
            'validUntil' => $this->validUntil->format(\DateTime::ATOM),
            'durationInMillis' => $this->durationInMillis,
        ];
    }
}
