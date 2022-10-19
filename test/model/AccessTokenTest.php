<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\test\model;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\AccessToken;
use PHPUnit\Framework\TestCase;

class AccessTokenTest extends TestCase
{
    public function testInvalidToken()
    {
        $this->expectException(\InvalidArgumentException::class);
        new AccessToken(null, new \DateTime(), 'durationInMillis');
    }

    public function testInvalidValidUntil()
    {
        $this->expectException(\InvalidArgumentException::class);
        new AccessToken('token', null, 'durationInMillis');
    }

    public function testInvalidDurationInMillis()
    {
        $this->expectException(\InvalidArgumentException::class);
        new AccessToken('token', new \DateTime(), null);
    }
}
