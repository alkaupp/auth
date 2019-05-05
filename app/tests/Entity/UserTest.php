<?php
declare(strict_types=1);

namespace Auth\Tests\Entity;

use Auth\AuthenticationException;
use Auth\Entity\Application\AppId;
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
            [$app]
        );
        $this->expectException(AuthenticationException::class);
        $user->authenticateTo($app, 'poopsydough');
    }

    public function testAuthenticateToThrowsAuthorizationException(): void
    {
        $user = new User(
            new EmailAddress('frank@example.com'),
            new BcryptPassword('poopsydoo'),
            [new ClientApplication(new AppId(), 'blaa', 'blaa', 'blaa')]
        );
        $this->expectException(AuthorizationException::class);
        $user->authenticateTo(new ClientApplication(new AppId(), 'bleu', 'bleu', 'bleu'), 'poopsydoo');
    }

    public function testAuthenticateToReturnsAuthenticationToken(): void
    {
        $app = new ClientApplication(new AppId(), 'blaa', 'blaa', 'blaa');
        $user = new User(
            new EmailAddress('frank@example.com'),
            new BcryptPassword('poopsydoo'),
            [$app]
        );
        $token = $user->authenticateTo($app, 'poopsydoo');
        $this->assertInstanceOf(AuthenticationToken::class, $token);
    }
}
