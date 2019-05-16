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
    public function testAuthenticateToThrowsAuthenticationException(): void
    {
        $app = new ClientApplication(new AppId(), 'blaa', 'blaa', 'blaa');
        $user = new User(
            new EmailAddress('frank@example.com'),
            new BcryptPassword('poopsydoo'),
            new Applications([$app])
        );
        $this->expectException(AuthenticationException::class);
        $user->verifyPassword('poopsydough');
    }

    public function testAuthenticateToThrowsAuthorizationException(): void
    {
        $app = new Applications([new ClientApplication(new AppId(), 'blaa', 'blaa', 'blaa')]);
        $user = new User(
            new EmailAddress('frank@example.com'),
            new BcryptPassword('poopsydoo'),
            $app
        );
        $this->expectException(AuthorizationException::class);
        $user->verifyPassword('poopsydoo');
        $app2 = new ClientApplication(new AppId(), 'bleu', 'bleu', 'bleu');
        $app2->authenticate($user);
    }

    public function testAuthenticateToReturnsAuthenticationToken(): void
    {
        $app = new ClientApplication(new AppId(), 'blaa', 'blaa', 'blaa');
        $user = new User(
            new EmailAddress('frank@example.com'),
            new BcryptPassword('poopsydoo'),
            new Applications([$app])
        );
        $user->verifyPassword('poopsydoo');
        $token = $app->authenticate($user);
        $this->assertInstanceOf(AuthenticationToken::class, $token);
    }
}
