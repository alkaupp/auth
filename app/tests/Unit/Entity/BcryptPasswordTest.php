<?php

declare(strict_types=1);

namespace Auth\Tests\Unit\Entity;

use Auth\Entity\User\BcryptPassword;
use PHPUnit\Framework\TestCase;

class BcryptPasswordTest extends TestCase
{
    public function testMatchesReturnsFalse(): void
    {
        $password = new BcryptPassword('supersecret');
        $this->assertFalse($password->matches('seecret'));
    }

    public function testMatchesReturnsTrue(): void
    {
        $password = new BcryptPassword('there4lD3al');
        $this->assertTrue($password->matches('there4lD3al'));
    }
}
