<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\OrderItem;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\ProductType;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\VatCategory;

class OrderItemBuilder
{
    /**
     * @return OrderItem
     */
    public static function makeCompleteOrderItem()
    {
        return OrderItem::createFrom([
            'id' => '15',
            'name' => 'Name',
            'description' => 'Description',
            'quantity' => 1,
            'amount' => Money::fromCents('EUR', 100),
            'tax' => Money::fromCents('EUR', 50),
            'category' => ProductType::DIGITAL,
            'vatCategory' => VatCategory::LOW,
        ]);
    }

    /**
     * @return OrderItem
     */
    public static function makeOrderItemWithoutOptionals()
    {
        return OrderItem::createFrom([
            'name' => 'Name',
            'description' => 'Description',
            'quantity' => 1,
            'amount' => Money::fromCents('EUR', 100),
            'category' => ProductType::DIGITAL,
        ]);
    }
}
