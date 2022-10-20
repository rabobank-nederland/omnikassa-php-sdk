<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\request;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request\InitiateRefundRequest;

class InitiateRefundRequestBuilder
{
    /**
     * Omit/nullify optional field.
     */
    public static function makeMinimalRequest(): InitiateRefundRequest
    {
        return new InitiateRefundRequest(
            Money::fromCents('EUR', 10),
            null,
            'LOW'
        );
    }

    public static function makeFullRequest(): InitiateRefundRequest
    {
        return new InitiateRefundRequest(
            Money::fromCents('EUR', 10),
            'Dit is een test',
            'LOW'
        );
    }
}
