<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use JsonMapper;

/**
 * Once an order is announced, an instance of this object will be returned.
 * You can use this object to retrieve the redirect URL to which the customer should be redirected.
 */
class MerchantOrderResponse
{
    /** @var string */
    private $redirectUrl;

    /** @var string */
    private $omnikassaOrderId;

    /**
     * Construct this response from the given json.
     *
     * @param string $json
     * @throws \JsonMapper_Exception
     */
    public function __construct($json)
    {
        if (empty($json)) {
            return;
        }
        $mapper = new JsonMapper();
        $mapper->map(json_decode($json), $this);
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @return string
     */
    public function getOmnikassaOrderId()
    {
        return $this->omnikassaOrderId;
    }

    /**
     * @param string $omnikassaOrderId
     */
    public function setOmnikassaOrderId($omnikassaOrderId)
    {
        $this->omnikassaOrderId = $omnikassaOrderId;
    }

    /**
     * @param string $redirectUrl
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * @return array
     */
    public function getSignatureData()
    {
        return [$this->redirectUrl];
    }
}
