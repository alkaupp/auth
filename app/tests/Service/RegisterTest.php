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
use Auth\RegisterException;
use Auth\Repository\ApplicationRepository;
use Auth\Repository\InMemoryApplicationRepository;
use Auth\Repository\InMemoryUserRepository;
use Auth\Repository\UserRepository;
use Auth\Service\Register;
use PHPUnit\Framework\TestCase;

class RegisterTest extends TestCase
{
    private const APP_ID = '54036de4-652a-11e9-8888-c5d1c66dcec3';
    private const DEFAULT_USERNAME = 'frank@example.com';
    private const DEFAULT_PASSWORD = 'supersecret';

    private UserRepository $userRepository;
    private ApplicationRepository $appRepository;
    private ClientApplication $app;

    protected function setUp()
    {
        $this->userRepository = new InMemoryUserRepository();
        $this->appRepository = new InMemoryApplicationRepository();
        $this->app = new ClientApplication(AppId::fromString(self::APP_ID), 'blaa', 'blaa', 'blaa');
        $this->appRepository->store($this->app);
    }

    public function testRegisterReturnsUser(): void
    {
        $register = new Register($this->userRepository, $this->appRepository);
        $user = $register(self::DEFAULT_USERNAME, self::DEFAULT_PASSWORD, self::APP_ID);
        $this->assertInstanceOf(User::class, $user);
    }

    public function testRegisterThrowsAuthenticationException(): void
    {
        $this->userRepository->store($this->createDefaultUser());
        $appId = new AppId();
        $this->appRepository->store(new ClientApplication($appId, 'myApp2', 'https://example.com', 'secrets'));
        $register = new Register($this->userRepository, $this->appRepository);
        $this->expectException(AuthenticationException::class);
        $register(self::DEFAULT_USERNAME, 'falsepassword', (string) $appId);
    }

    public function testRegisterStoresNewApplicationForUser(): void
    {
        $user = $this->createDefaultUser();
        $this->userRepository->store($user);
        $appId = new AppId();
        $app = new ClientApplication($appId, 'myApp2', 'https://example.com', 'secrets');
        $this->appRepository->store($app);
        $register = new Register($this->userRepository, $this->appRepository);
        $register(self::DEFAULT_USERNAME, self::DEFAULT_PASSWORD, (string) $appId);
        $this->assertTrue($user->hasApplication($app));
    }

    private function createDefaultUser(): User
    {
        return new User(
            new EmailAddress(self::DEFAULT_USERNAME),
            new BcryptPassword(self::DEFAULT_PASSWORD),
            new Applications([$this->app])
        );
    }
}
