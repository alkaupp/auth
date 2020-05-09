<?php

declare(strict_types=1);

namespace Auth\Tests\Service;

use Auth\AuthenticationException;
use Auth\Entity\Application\AppId;
use Auth\Entity\Application\Applications;
use Auth\Entity\Application\AuthenticationToken;
use Auth\Entity\Application\ClientApplication;
use Auth\Entity\User\BcryptPassword;
use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\User;
use Auth\AuthorizationException;
use Auth\Repository\ApplicationRepository;
use Auth\Repository\InMemoryApplicationRepository;
use Auth\Repository\InMemoryUserRepository;
use Auth\Repository\NotFoundException;
use Auth\Repository\UserRepository;
use Auth\Service\SignIn;
use PHPUnit\Framework\TestCase;

class SignInTest extends TestCase
{
    private UserRepository $userRepository;
    private ApplicationRepository $appRepository;
    private ClientApplication $app;

    protected function setUp()
    {
        $this->userRepository = new InMemoryUserRepository();
        $this->appRepository = new InMemoryApplicationRepository();
        $this->app = new ClientApplication(new AppId(), "blaa", "blaa", "blaa");
    }

    public function testSignInThrowsUserNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('User not found with email frank@example.com');
        $this->appRepository->store($this->app);
        $signIn = new SignIn($this->userRepository, $this->appRepository);
        $signIn('frank@example.com', 'secrets', $this->app->appId()->__toString());
    }

    public function testSignInThrowsAuthenticationException(): void
    {
        $signIn = new SignIn($this->userRepository, $this->appRepository);
        $this->userRepository->store($this->createUser('frank@example.com', 'secrets'));
        $this->appRepository->store($this->app);
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid password');
        $signIn('frank@example.com', 'sheecrets', $this->app->appId()->__toString());
    }

    public function testSignInReturnsToken(): void
    {
        $signIn = new SignIn($this->userRepository, $this->appRepository);
        $this->userRepository->store($this->createUser('frank@example.com', 'secrets'));
        $this->appRepository->store($this->app);
        $token = $signIn('frank@example.com', 'secrets', $this->app->appId()->__toString());
        $this->assertInstanceOf(AuthenticationToken::class, $token);
    }

    public function testSignInThrowsAppNotFoundException(): void
    {
        $signIn = new SignIn($this->userRepository, $this->appRepository);
        $this->userRepository->store($this->createUser('frank@example.com', 'secrets'));
        $this->expectException(NotFoundException::class);
        $signIn('frank@example.com', 'secrets', (new AppId())->__toString());
    }

    public function testSignInThrowsAuthorizationException(): void
    {
        $signIn = new SignIn($this->userRepository, $this->appRepository);
        $this->userRepository->store($this->createUser('frank@example.com', 'secrets'));
        $appId = new AppId();
        $this->appRepository->store(new ClientApplication($appId, 'something', 'something', 'something'));
        $this->expectException(AuthorizationException::class);
        $signIn('frank@example.com', 'secrets', $appId->__toString());
    }

    private function createUser(string $email, string $password): User
    {
        return new User(
            new EmailAddress($email),
            new BcryptPassword($password),
            new Applications([$this->app])
        );
    }
}
