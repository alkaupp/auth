<?php
declare(strict_types=1);

namespace Auth\Tests\Entity;

use Auth\AuthenticationException;
use Auth\Entity\Application\AppId;
use Auth\Entity\Application\ClientApplication;
use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\BcryptPassword;
use Auth\Entity\User\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testAuthenticateThrowsException(): void
    {
        $user = new User(
            new EmailAddress('frank@example.com'),
            new BcryptPassword('poopsydoo'),
            new ClientApplication(new AppId(), "blaa", "blaa", "blaa")
        );
        $this->expectException(AuthenticationException::class);
        $user->authenticate('poopsydough');
    }
}
