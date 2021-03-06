<?php

declare(strict_types=1);

namespace Auth\Service;

use Auth\AuthenticationException;
use Auth\Entity\User\EmailAddress;
use Auth\Repository\NotFoundException;
use Auth\Repository\UserRepository;

final class PasswordChange
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $username
     * @param string $oldPassword
     * @param string $newPassword
     * @throws NotFoundException
     * @throws AuthenticationException
     */
    public function __invoke(string $username, string $oldPassword, string $newPassword): void
    {
        $user = $this->userRepository->getByEmailAddress(new EmailAddress($username));
        $user->changePassword($oldPassword, $newPassword);
        $this->userRepository->store($user);
    }
}
