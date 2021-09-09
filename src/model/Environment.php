<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model;

/**
 * This class contains the ROFE API urls per environment.
 */
abstract class Environment
{
    public const PRODUCTION = 'https://betalen.rabobank.nl/omnikassa-api/';
    public const SANDBOX = 'https://betalen.rabobank.nl/omnikassa-api-sandbox/';
}
