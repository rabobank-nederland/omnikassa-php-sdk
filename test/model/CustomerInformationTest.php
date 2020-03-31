<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\CustomerInformation;
use PHPUnit_Framework_TestCase;

class CustomerInformationTest extends PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        $customerInformation = $this->makeCompleteCustomerInformation();

        $this->assertEquals('jan.van.veen@gmail.com', $customerInformation->getEmailAddress());
        $this->assertEquals('20-03-1987', $customerInformation->getDateOfBirth());
        $this->assertEquals('M', $customerInformation->getGender());
        $this->assertEquals('J.M.', $customerInformation->getInitials());
        $this->assertEquals('0204971111', $customerInformation->getTelephoneNumber());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionIsThrownForInvalidProperty()
    {
        CustomerInformation::createFrom(['emailAdress' => 'test']);
    }

    public function testJsonSerialize()
    {
        $expectedJson = [
            'emailAddress' => 'jan.van.veen@gmail.com',
            'dateOfBirth' => '20-03-1987',
            'gender' => 'M',
            'initials' => 'J.M.',
            'telephoneNumber' => '0204971111',
        ];
        $customerInformation = $this->makeCompleteCustomerInformation();
        $actualJson = $customerInformation->jsonSerialize();

        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testJsonSerialize_withNullValues()
    {
        $expectedJson = [];
        $customerInformation = $this->makeCustomerInformationWithoutOptionals();
        $actualJson = $customerInformation->jsonSerialize();

        $this->assertEquals($expectedJson, $actualJson);
    }

    /**
     * @return CustomerInformation
     */
    private function makeCompleteCustomerInformation()
    {
        return CustomerInformation::createFrom([
            'emailAddress' => 'jan.van.veen@gmail.com',
            'dateOfBirth' => '20-03-1987',
            'gender' => 'M',
            'initials' => 'J.M.',
            'telephoneNumber' => '0204971111',
        ]);
    }

    /**
     * @return CustomerInformation
     */
    private function makeCustomerInformationWithoutOptionals()
    {
        return CustomerInformation::createFrom([]);
    }
}
