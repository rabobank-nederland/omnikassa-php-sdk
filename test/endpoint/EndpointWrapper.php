<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\endpoint;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\Connector;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\endpoint\Endpoint;

class EndpointWrapper extends Endpoint
{
    public function __construct(Connector $connector, $signingKey)
    {
        parent::__construct($connector, $signingKey);
    }
}
