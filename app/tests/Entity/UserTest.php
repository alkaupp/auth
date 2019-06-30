<?php
declare(strict_types=1);

namespace Auth\Tests\Entity;

use Auth\AuthenticationException;
use Auth\Entity\Application\AppId;
use Auth\Entity\Application\Applications;
use Auth\Entity\Application\AuthenticationToken;
use Auth\Entity\Application\ClientApplication;
use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\BcryptPassword;
use Auth\Entity\User\User;
use Auth\AuthorizationException;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private const DEFAULT_USERNAME = 'frank@example.com';
    private const DEFAULT_PASSWORD = 'poopsydoo';
    public function testAuthenticateToThrowsAuthenticationException(): void
    {
        $user = $this->createUser(new Applications([$this->createApplication()]));
        $this->expectException(AuthenticationException::class);
        $user->verifyPassword('poopsydough');
    }

    public function testAuthenticateToThrowsAuthorizationException(): void
    {
        $user = $this->createUser(new Applications([$this->createApplication()]));
        $this->expectException(AuthorizationException::class);
        $user->verifyPassword(self::DEFAULT_PASSWORD);
        $app2 = new ClientApplication(new AppId(), 'bleu', 'bleu', 'bleu');
        $app2->authenticate($user);
    }

    public function testAuthenticateToReturnsAuthenticationToken(): void
    {
        $app = $this->createApplication();
        $user = $this->createUser(new Applications([$app]));
        $user->verifyPassword(self::DEFAULT_PASSWORD);
        $token = $app->authenticate($user);
        $this->assertInstanceOf(AuthenticationToken::class, $token);
    }

    public function testChangePasswordThrowsAuthenticationException(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage("Invalid password");
        $this->createUser(new Applications())->changePassword('old password', 'new password');
    }

    public function testChangePasswordIsChanged(): void
    {
        $user = $this->createUser(new Applications());
        $user->verifyPassword(self::DEFAULT_PASSWORD);
        $user->changePassword(self::DEFAULT_PASSWORD, 'my new password');
        $user->verifyPassword('my new password');
        $this->assertTrue($user->isAuthenticated());
    }

    private function createUser(Applications $applications): User
    {
        return new User(
            new EmailAddress(self::DEFAULT_USERNAME),
            new BcryptPassword(self::DEFAULT_PASSWORD),
            $applications
        );
    }

    private function createApplication(): ClientApplication
    {
        return new ClientApplication(new AppId(), 'blaa', 'blaa', 'blaa');
    }
}
