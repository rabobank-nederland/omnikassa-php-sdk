<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\connector;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\ApiConnector;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\http\RESTTemplate;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\TokenProvider;

class ApiConnectorWrapper extends ApiConnector
{
    public function __construct(RESTTemplate $restTemplate, TokenProvider $tokenProvider)
    {
        parent::__construct($restTemplate, $tokenProvider);
    }
}
