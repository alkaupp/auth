<?php
declare(strict_types=1);

namespace Auth\Tests\Service;

use Auth\Entity\User\User;
use Auth\RegisterException;
use Auth\Repository\InMemoryUserRepository;
use Auth\Service\Register;
use PHPUnit\Framework\TestCase;

class RegisterTest extends TestCase
{
    public function testRegisterThrowsUserExistsError(): void
    {
        $register = new Register(new InMemoryUserRepository());
        $register("frank@example.com", "sosecret");
        $this->expectException(RegisterException::class);
        $this->expectExceptionMessage("Username is already taken.");
        $register("frank@example.com", "sosecret");
    }

    public function testRegisterReturnsUser(): void
    {
        $register = new Register(new InMemoryUserRepository());
        $user = $register("frank@example.com", "sosecret");
        $this->assertInstanceOf(User::class, $user);
    }
}
