<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request;

use InvalidArgumentException;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\VatCategory;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model\request\InitiateRefundRequestBuilder;
use PHPUnit\Framework\TestCase;

class InitiateRefundRequestTest extends TestCase
{
    public function testJsonEncodingWithoutDescription()
    {
        $merchantOrderRequest = InitiateRefundRequestBuilder::makeMinimalRequest();
        $expectedJson = json_encode([
            'money' => ['currency' => 'EUR', 'amount' => 10],
            'description' => null,
            'vatCategory' => 'LOW',
        ]);

        $actualJson = json_encode($merchantOrderRequest);

        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testJsonEncoding()
    {
        $merchantOrderRequest = InitiateRefundRequestBuilder::makeFullRequest();
        $expectedJson = json_encode([
            'money' => ['currency' => 'EUR', 'amount' => 10],
            'description' => 'Dit is een test',
            'vatCategory' => 'LOW',
        ]);

        $actualJson = json_encode($merchantOrderRequest);

        $this->assertEquals($expectedJson, $actualJson);
    }

    /**
     * Ensure every mapping possible maps correctly.
     */
    public function testVatCategoryMapping()
    {
        $testData = [
            // Input => Assertion
            VatCategory::HIGH => 'HIGH',
            VatCategory::LOW => 'LOW',
            VatCategory::ZERO => 'ZERO',
            VatCategory::NONE => null,
            'HIGH' => 'HIGH',
            'LOW' => 'LOW',
            'ZERO' => 'ZERO',
            null => null,
        ];

        foreach ($testData as $inputVatCategory => $expectedVatCategory) {
            $subject = new InitiateRefundRequest(
                Money::fromCents('EUR', 10),
                null,
                $inputVatCategory
            );

            $this->assertEquals($expectedVatCategory, $subject->jsonSerialize()['vatCategory']);
        }
    }

    /**
     * Unsupported values should throw an error.
     */
    public function testVatCategoryValidation()
    {
        $this->expectException(InvalidArgumentException::class);

        new InitiateRefundRequest(
            Money::fromCents('EUR', 10),
            null,
            'unsupported'  // Error
        );
    }
}
