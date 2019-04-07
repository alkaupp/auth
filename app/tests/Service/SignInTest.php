<?php
declare(strict_types=1);

namespace Auth\Tests\Service;

use Auth\AuthenticationException;
use Auth\Entity\User\BcryptPassword;
use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\User;
use Auth\Repository\InMemoryUserRepository;
use Auth\Repository\NotFoundException;
use Auth\Repository\UserRepository;
use Auth\Service\SignIn;
use PHPUnit\Framework\TestCase;

class SignInTest extends TestCase
{
    /** @var UserRepository */
    private $userRepository;

    protected function setUp()
    {
        $this->userRepository = new InMemoryUserRepository();
    }

    public function testSignInThrowsUserNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('User not found with email frank@example.com');
        $signIn = new SignIn($this->userRepository);
        $signIn('frank@example.com', 'secrets');
    }

    public function testSignInThrowsAuthenticationException(): void
    {
        $signIn = new SignIn($this->userRepository);
        $this->userRepository->store(new User(new EmailAddress('frank@example.com'), new BcryptPassword('secrets')));
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid password');
        $signIn('frank@example.com', 'sheecrets');
    }

    public function testSignInReturnsUser(): void
    {
        $signIn = new SignIn($this->userRepository);
        $this->userRepository->store(new User(new EmailAddress('frank@example.com'), new BcryptPassword('secrets')));
        $user = $signIn('frank@example.com', 'secrets');
        $this->assertInstanceOf(User::class, $user);
    }
}
