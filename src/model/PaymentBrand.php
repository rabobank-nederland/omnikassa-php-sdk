<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model;

/**
 * This class houses the different types of payment brands that can be included in the MerchantOrder to restrict
 * the payment brands that the consumer can choose from.
 */
class PaymentBrand
{
    public const IDEAL = 'IDEAL';
    public const AFTERPAY = 'AFTERPAY';
    public const PAYPAL = 'PAYPAL';
    public const MASTERCARD = 'MASTERCARD';
    public const VISA = 'VISA';
    public const BANCONTACT = 'BANCONTACT';
    public const MAESTRO = 'MAESTRO';
    public const V_PAY = 'V_PAY';
    public const SOFORT = 'SOFORT';

    /**
     * The CARDS type comprises MASTERCARD, VISA, BANCONTACT, MAESTRO and V_PAY.
     */
    public const CARDS = 'CARDS';
}
