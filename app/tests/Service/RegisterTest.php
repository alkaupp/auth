<?php
declare(strict_types=1);

namespace Auth\Tests\Service;

use Auth\Entity\Application\AppId;
use Auth\Entity\Application\ClientApplication;
use Auth\Entity\User\User;
use Auth\RegisterException;
use Auth\Repository\ApplicationRepository;
use Auth\Repository\InMemoryApplicationRepository;
use Auth\Repository\InMemoryUserRepository;
use Auth\Service\Register;
use PHPUnit\Framework\TestCase;

class RegisterTest extends TestCase
{
    private const APP_ID = '54036de4-652a-11e9-8888-c5d1c66dcec3';

    /** @var ApplicationRepository */
    private $appRepository;

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->appRepository = new InMemoryApplicationRepository();
        $this->appRepository->store(new ClientApplication(AppId::fromString(self::APP_ID), "blaa", "blaa", "blaa"));
    }

    public function testRegisterThrowsUserExistsError(): void
    {
        $register = new Register(new InMemoryUserRepository(), $this->appRepository);
        $register("frank@example.com", "sosecret", self::APP_ID);
        $this->expectException(RegisterException::class);
        $this->expectExceptionMessage("Username is already taken.");
        $register("frank@example.com", "sosecret", self::APP_ID);
    }

    public function testRegisterReturnsUser(): void
    {
        $register = new Register(new InMemoryUserRepository(), $this->appRepository);
        $user = $register("frank@example.com", "sosecret", self::APP_ID);
        $this->assertInstanceOf(User::class, $user);
    }
}
