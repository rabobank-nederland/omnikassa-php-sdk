<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model;

/**
 * Class OrderItem.
 */
class OrderItem implements \JsonSerializable
{
    /** @var string */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $description;
    /** @var int */
    private $quantity;
    /** @var Money */
    private $amount;
    /** @var Money */
    private $tax;
    /** @var string */
    private $category;
    /** @var string */
    private $vatCategory;

    /**
     * @param string $name
     * @param string $description
     * @param int    $quantity    describes how many of this item the customer ordered
     * @param Money  $amount      describes the price per item
     * @param Money  $tax         describes the tax per item
     * @param string $category
     *
     * @deprecated This constructor is deprecated but remains available for backwards compatibility. Use the static
     * createFrom method instead.
     * @see OrderItem::createFrom()
     */
    public function __construct($name, $description, $quantity, $amount, $tax, $category)
    {
        $this->name = $name;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->amount = $amount;
        $this->tax = $tax;
        $this->category = $category;
    }

    public static function createFrom(array $data)
    {
        $orderItem = new OrderItem(null, null, null, null, null, null);
        foreach ($data as $key => $value) {
            if (property_exists($orderItem, $key)) {
                $orderItem->$key = $data[(string) $key];
            } else {
                $properties = implode(', ', array_keys(get_object_vars($orderItem)));
                throw new \InvalidArgumentException("Invalid property {$key} supplied. Valid properties for OrderItem are: {$properties}");
            }
        }

        return $orderItem;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return Money
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return Money
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return string
     */
    public function getVatCategory()
    {
        return $this->vatCategory;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $json = [];
        foreach ($this as $key => $value) {
            if (null !== $value) {
                $json[$key] = $value;
            }
        }

        return $json;
    }
}
