<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model;

use InvalidArgumentException;
use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\Address;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    public function testConstruction()
    {
        $address = $this->makeFullAddress();

        $this->assertEquals('Jan', $address->getFirstName());
        $this->assertEquals('van', $address->getMiddleName());
        $this->assertEquals('Veen', $address->getLastName());
        $this->assertEquals('Voorbeeldstraat', $address->getStreet());
        $this->assertEquals('1234AB', $address->getPostalCode());
        $this->assertEquals('Haarlem', $address->getCity());
        $this->assertEquals('NL', $address->getCountryCode());
        $this->assertEquals('5', $address->getHouseNumber());
        $this->assertEquals('a', $address->getHouseNumberAddition());
    }

    public function testExceptionIsThrownForInvalidProperty()
    {
        $this->expectException(InvalidArgumentException::class);

        Address::createFrom(['firstname' => 'test']);
    }

    public function testJsonSerialize()
    {
        $expectedJson = [
            'firstName' => 'Jan',
            'middleName' => 'van',
            'lastName' => 'Veen',
            'street' => 'Voorbeeldstraat',
            'houseNumber' => '5',
            'houseNumberAddition' => 'a',
            'postalCode' => '1234AB',
            'city' => 'Haarlem',
            'countryCode' => 'NL',
        ];
        $address = $this->makeFullAddress();
        $actualJson = $address->jsonSerialize();

        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testJsonSerializeWithNullValues()
    {
        $expectedJson = [
            'firstName' => 'Jan',
            'lastName' => 'Veen',
            'street' => 'Voorbeeldstraat',
            'postalCode' => '1234AB',
            'city' => 'Haarlem',
            'countryCode' => 'NL',
        ];
        $address = $this->makeSmallAddress();
        $actualJson = $address->jsonSerialize();

        $this->assertEquals($expectedJson, $actualJson);
    }

    /**
     * @return Address
     */
    private function makeFullAddress()
    {
        return Address::createFrom([
            'firstName' => 'Jan',
            'middleName' => 'van',
            'lastName' => 'Veen',
            'street' => 'Voorbeeldstraat',
            'postalCode' => '1234AB',
            'city' => 'Haarlem',
            'countryCode' => 'NL',
            'houseNumber' => '5',
            'houseNumberAddition' => 'a',
        ]);
    }

    /**
     * @return Address
     */
    private function makeSmallAddress()
    {
        return Address::createFrom([
            'firstName' => 'Jan',
            'lastName' => 'Veen',
            'street' => 'Voorbeeldstraat',
            'postalCode' => '1234AB',
            'city' => 'Haarlem',
            'countryCode' => 'NL',
        ]);
    }
}
