<?php

declare(strict_types=1);

namespace Auth\Tests\Service;

use Auth\AuthenticationException;
use Auth\Entity\Application\Applications;
use Auth\Entity\User\BcryptPassword;
use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\User;
use Auth\Repository\InMemoryUserRepository;
use Auth\Repository\NotFoundException;
use Auth\Service\PasswordChange;
use PHPUnit\Framework\TestCase;

class PasswordChangeTest extends TestCase
{
    public function testPasswordChangeThrowsNotFoundException(): void
    {
        $userRepository = new InMemoryUserRepository();
        $passwordChange = new PasswordChange($userRepository);
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('User not found with email frank@example.com');
        $passwordChange('frank@example.com', 'smart fella', 'fart smella');
    }

    public function testPasswordChangeThrowsAuthenticationException(): void
    {
        $user = new User(
            new EmailAddress('frank@example.com'),
            new BcryptPassword('smart fella'),
            new Applications()
        );
        $userRepository = new InMemoryUserRepository();
        $userRepository->store($user);
        $passwordChange = new PasswordChange($userRepository);
        $this->expectException(AuthenticationException::class);
        $passwordChange('frank@example.com', 'old password', 'new password');
    }

    public function testPasswordIsChanged(): void
    {
        $user = new User(
            new EmailAddress('frank@example.com'),
            new BcryptPassword('old password'),
            new Applications()
        );
        $userRepository = new InMemoryUserRepository();
        $userRepository->store($user);
        $passwordChange = new PasswordChange($userRepository);
        $passwordChange('frank@example.com', 'old password', 'new password');
        $user->verifyPassword('new password');
        $this->assertTrue($user->isAuthenticated());
    }
}
