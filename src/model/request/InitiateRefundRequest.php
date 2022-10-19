<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\VatCategory;

/**
 * Class for initiating refund.
 */
class InitiateRefundRequest implements \JsonSerializable
{
    /** @var Money */
    private $money;
    /** @var string|null */
    private $description;
    /** @var string|null */
    private $vatCategory;

    /**
     * @param Money       $money       the amount to be refunded
     * @param string|null $description optional description for this refund
     * @param string|null $vatCategory VAT type. Either use one of the static values in VatCategory or pass one of: "HIGH" / "LOW" / "ZERO" / null (= None apply)
     */
    public function __construct(Money $money, ?string $description, ?string $vatCategory)
    {
        switch ($vatCategory) {
            // Aliases.
            case VatCategory::HIGH: $vatCategoryValue = 'HIGH'; break;
            case VatCategory::LOW: $vatCategoryValue = 'LOW'; break;
            case VatCategory::ZERO: $vatCategoryValue = 'ZERO'; break;
            case VatCategory::NONE: $vatCategoryValue = null; break;
            // As-is.
            case 'HIGH':
            case 'LOW':
            case 'ZERO':
            case null:
                $vatCategoryValue = $vatCategory;
                break;
            default:
                throw new \InvalidArgumentException('Invalid $vatCategory given. Either use one of the static values in VatCategory or pass one of: "HIGH", "LOW", "ZERO", null');
        }

        $this->money = $money;
        $this->description = $description;
        $this->vatCategory = $vatCategoryValue;
    }

    /** {@inheritDoc} */
    public function jsonSerialize()
    {
        return [
            'money' => $this->money,
            'description' => $this->description,
            'vatCategory' => $this->vatCategory,
        ];
    }
}
