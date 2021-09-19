<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model;

/**
 * This class houses the different types of VAT categories that can be assigned to a given order item.
 */
class VatCategory
{
    public const HIGH = '1';
    public const LOW = '2';
    public const ZERO = '3';
    public const NONE = '4';
}
