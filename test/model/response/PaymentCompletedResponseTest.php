<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response;

use InvalidArgumentException;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\response\PaymentCompletedResponse;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SigningKey;
use PHPUnit\Framework\TestCase;

class PaymentCompletedResponseTest extends TestCase
{
    public function testThatIsValidReturnsTrueForAValidSignature()
    {
        $signingKey = new SigningKey('secret');
        $paymentCompletedResponse = PaymentCompletedResponse::createInstance('1', 'COMPLETED', 'b890b2f3c6f102bb853ed448dd58d2c13cc695541f5eecca713470e68ced6f2c1a5f5ddd529a732ff51a019126ffefa8bd1d0193b596b393339ffcbf6f335241', $signingKey);
        $this->assertNotFalse($paymentCompletedResponse);
        $this->assertEquals('1', $paymentCompletedResponse->getOrderID());
        $this->assertEquals('COMPLETED', $paymentCompletedResponse->getStatus());
    }

    public function testThatIsValidReturnsFalseForInvalidSignatures()
    {
        $signingKey = new SigningKey('secret');
        $isValid = PaymentCompletedResponse::createInstance('1', 'CANCELLED', 'ffb94fef027526bab3f98eaa432974daea4e743f09de86ab732208497805bb12', $signingKey);
        $this->assertFalse($isValid, 'The given payment complete response was valid, but should be invalid');
    }

    public function testThatIsValidReturnsTrueForUnderscoreInStatus()
    {
        $signingKey = new SigningKey('secret');
        $paymentCompletedResponse = PaymentCompletedResponse::createInstance('1', 'IN_PROGRESS', '1a551027bc3cc041a56b9efa252640c76b2e5815f816dd123fa1b32b4683729e904b5fa711870b956f1d9b16c714168d129068a48f875c2f91185d6c18eccf61', $signingKey);
        $this->assertNotFalse($paymentCompletedResponse);
        $this->assertEquals('1', $paymentCompletedResponse->getOrderID());
        $this->assertEquals('IN_PROGRESS', $paymentCompletedResponse->getStatus());
    }

    public function testThatLettersinOrderIDIsValid()
    {
        $signingKey = new SigningKey('secret');
        $paymentCompletedResponse = PaymentCompletedResponse::createInstance('Test1234', 'COMPLETED', 'bf4f5b787d954296b9c2e15028c2311df5e31a3d94c540e361faf1d0951b7858041089d430e17730f1efd3a308881c094355f55e09b993ca53f2063859d1eb4b', $signingKey);
        $this->assertNotFalse($paymentCompletedResponse);
        $this->assertEquals('Test1234', $paymentCompletedResponse->getOrderID());
        $this->assertEquals('COMPLETED', $paymentCompletedResponse->getStatus());
    }

    public function testThatItThrowsAnExceptionOnABadOrderIdFormat()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('One or more parameters in the merchantReturnUrl did not match the required format.');

        $signingKey = new SigningKey('secret');
        PaymentCompletedResponse::createInstance('!Invalid', 'COMPLETED', 'bf4f5b787d954296b9c2e15028c2311df5e31a3d94c540e361faf1d0951b7858041089d430e17730f1efd3a308881c094355f55e09b993ca53f2063859d1eb4b', $signingKey);
    }

    public function testThatItThrowsAnExceptionOnABadStatusFormat()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('One or more parameters in the merchantReturnUrl did not match the required format.');

        $signingKey = new SigningKey('secret');
        PaymentCompletedResponse::createInstance('Test1234', '!Invalid', 'bf4f5b787d954296b9c2e15028c2311df5e31a3d94c540e361faf1d0951b7858041089d430e17730f1efd3a308881c094355f55e09b993ca53f2063859d1eb4b', $signingKey);
    }

    public function testThatItThrowsAnExceptionOnABadSignatureFormat()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('One or more parameters in the merchantReturnUrl did not match the required format.');

        $signingKey = new SigningKey('secret');
        PaymentCompletedResponse::createInstance('Test1234', 'COMPLETED', '!Invalid', $signingKey);
    }
}
