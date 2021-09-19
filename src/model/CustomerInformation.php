<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model;

/**
 * Class CustomerInformation.
 */
class CustomerInformation implements \JsonSerializable
{
    /** @var string */
    private $emailAddress;
    /** @var string */
    private $dateOfBirth;
    /** @var string */
    private $gender;
    /** @var string */
    private $initials;
    /** @var string */
    private $telephoneNumber;
    /** @var string */
    private $fullName;

    /**
     * @param string $emailAddress
     * @param string $dateOfBirth
     * @param string $gender
     * @param string $initials
     * @param string $telephoneNumber
     *
     * @deprecated This constructor is deprecated but remains available for backwards compatibility. Use the static
     * createFrom method instead.
     * @see CustomerInformation::createFrom()
     */
    public function __construct($emailAddress, $dateOfBirth, $gender, $initials, $telephoneNumber, $fullName = null)
    {
        $this->emailAddress = $emailAddress;
        $this->dateOfBirth = $dateOfBirth;
        $this->gender = $gender;
        $this->initials = $initials;
        $this->telephoneNumber = $telephoneNumber;
        $this->fullName = $fullName;
    }

    public static function createFrom(array $data)
    {
        $customerInformation = new CustomerInformation(null, null, null, null, null, null);
        foreach ($data as $key => $value) {
            if (property_exists($customerInformation, $key)) {
                $customerInformation->$key = $data[(string) $key];
            } else {
                $properties = implode(', ', array_keys(get_object_vars($customerInformation)));
                throw new \InvalidArgumentException("Invalid property {$key} supplied. Valid properties for CustomerInformation are: {$properties}");
            }
        }

        return $customerInformation;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @return string
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @return string
     */
    public function getInitials()
    {
        return $this->initials;
    }

    /**
     * @return string
     */
    public function getTelephoneNumber()
    {
        return $this->telephoneNumber;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
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
