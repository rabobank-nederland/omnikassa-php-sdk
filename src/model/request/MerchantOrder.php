<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model\request;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Address;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\CustomerInformation;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Money;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\OrderItem;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\PaymentBrandMetaData;

/**
 * Class MerchantOrder.
 */
class MerchantOrder implements \JsonSerializable
{
    /** @var string */
    private $merchantOrderId;
    /** @var string */
    private $description;
    /** @var OrderItem[] */
    private $orderItems;
    /** @var Money */
    private $amount;
    /** @var Address */
    private $shippingDetail;
    /** @var Address */
    private $billingDetail;
    /** @var CustomerInformation */
    private $customerInformation;
    /** @var string */
    private $language;
    /** @var string */
    private $merchantReturnURL;
    /** @var string */
    private $paymentBrand;
    /** @var string */
    private $paymentBrandForce;
    /** @var string */
    private $initiatingParty;
    /** @var bool */
    private $skipHppResultPage = false;
    /** @var PaymentBrandMetaData */
    private $paymentBrandMetaData;

    /**
     * @param string              $merchantOrderId
     * @param string              $description
     * @param OrderItem[]         $orderItems
     * @param Money               $amount
     * @param Address             $shippingDetails
     * @param string              $language
     * @param string              $merchantReturnURL
     * @param string              $paymentBrand
     * @param string              $paymentBrandForce
     * @param CustomerInformation $customerInformation
     * @param Address             $billingDetails
     * @param $initiatingParty
     * @param bool $skipHppResultPage
     *
     * @deprecated This constructor is deprecated but remains available for backwards compatibility. Use the static
     * createFrom method instead.
     * @see MerchantOrder::createFrom()
     */
    public function __construct(
        $merchantOrderId,
        $description,
        $orderItems,
        $amount,
        $shippingDetails,
        $language,
        $merchantReturnURL,
        $paymentBrand = null,
        $paymentBrandForce = null,
        $customerInformation = null,
        $billingDetails = null,
        $initiatingParty = null,
        $skipHppResultPage = false,
        $paymentBrandMetaData = null
    ) {
        $this->merchantOrderId = $merchantOrderId;
        $this->description = $description;
        $this->orderItems = $orderItems;
        $this->amount = $amount;
        $this->shippingDetail = $shippingDetails;
        $this->customerInformation = $customerInformation;
        $this->billingDetail = $billingDetails;
        $this->language = $language;
        $this->merchantReturnURL = $merchantReturnURL;
        $this->paymentBrand = $paymentBrand;
        $this->paymentBrandForce = $paymentBrandForce;
        $this->initiatingParty = $initiatingParty;
        $this->skipHppResultPage = $skipHppResultPage;
        $this->paymentBrandMetaData = $paymentBrandMetaData;
    }

    public static function createFrom(array $data)
    {
        $merchantOrder = new MerchantOrder(null, null, null, null, null, null, null, null);
        foreach ($data as $key => $value) {
            if (property_exists($merchantOrder, $key)) {
                $merchantOrder->$key = $data[(string) $key];
            } else {
                $properties = implode(', ', array_keys(get_object_vars($merchantOrder)));
                throw new \InvalidArgumentException("Invalid property {$key} supplied. Valid properties for MerchantOrder are: {$properties}");
            }
        }

        return $merchantOrder;
    }

    /**
     * @return string
     */
    public function getMerchantOrderId()
    {
        return $this->merchantOrderId;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return OrderItem[]
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * @return Money
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return Address
     */
    public function getShippingDetail()
    {
        return $this->shippingDetail;
    }

    /**
     * @return Address
     */
    public function getBillingDetail()
    {
        return $this->billingDetail;
    }

    /**
     * @return CustomerInformation
     */
    public function getCustomerInformation()
    {
        return $this->customerInformation;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getMerchantReturnURL()
    {
        return $this->merchantReturnURL;
    }

    /**
     * @return string
     */
    public function getPaymentBrand()
    {
        return $this->paymentBrand;
    }

    /**
     * @return string
     */
    public function getPaymentBrandForce()
    {
        return $this->paymentBrandForce;
    }

    /**
     * @return string
     */
    public function getInitiatingParty()
    {
        return $this->initiatingParty;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $json = [];
        foreach ($this as $key => $value) {
            if (null !== $value) {
                if ('paymentBrandMetaData' === $key && !$value->hasProperties()) {
                    continue;
                }

                $json[$key] = $value;
            }
        }

        return $json;
    }

    public function getSkipHppResultPage(): bool
    {
        return $this->skipHppResultPage;
    }

    public function getPaymentBrandMetaData(): ?PaymentBrandMetaData
    {
        return $this->paymentBrandMetaData;
    }
}
