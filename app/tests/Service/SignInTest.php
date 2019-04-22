<?php
declare(strict_types=1);

namespace Auth\Tests\Service;

use Auth\AuthenticationException;
use Auth\Entity\Application\AppId;
use Auth\Entity\Application\ClientApplication;
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
        $this->userRepository->store($this->createUser('frank@example.com', 'secrets'));
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid password');
        $signIn('frank@example.com', 'sheecrets');
    }

    public function testSignInReturnsUser(): void
    {
        $signIn = new SignIn($this->userRepository);
        $this->userRepository->store($this->createUser('frank@example.com', 'secrets'));
        $user = $signIn('frank@example.com', 'secrets');
        $this->assertInstanceOf(User::class, $user);
    }

    private function createUser(string $email, string $password): User
    {
        return new User(
            new EmailAddress($email),
            new BcryptPassword($password),
            new ClientApplication(new AppId(), "blaa", "blaa", "blaa")
        );
    }
}
