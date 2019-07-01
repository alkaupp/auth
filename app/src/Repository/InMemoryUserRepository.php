<?php
declare(strict_types=1);

namespace Auth\Repository;

use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\User;
use Auth\Entity\User\UserId;

final class InMemoryUserRepository implements UserRepository
{
    /** @var User[] */
    private array $users;

    public function __construct()
    {
        $this->users = [];
    }

    public function getById(UserId $userId): User
    {
        /** @var User $user */
        foreach ($this->users as $user) {
            if ($user->userId()->equals($userId)) {
                return $user;
            }
        }
        throw new NotFoundException(sprintf('User not found with id %s', $userId));
    }

    public function getByEmailAddress(EmailAddress $emailAddress): User
    {
        foreach ($this->users as $user) {
            if ($user->email()->equals($emailAddress)) {
                return $user;
            }
        }
        throw new NotFoundException(sprintf('User not found with email %s', $emailAddress));
    }

    public function store(User $user): void
    {
        if (!$this->exists($user)) {
            $this->users[] = $user;
        }
    }

    private function exists(User $user): bool
    {
        foreach ($this->users as $existingUser) {
            if ($existingUser->equals($user)) {
                return true;
            }
        }
        return false;
    }

    public function remove(UserId $userId): void
    {
        foreach ($this->users as $index => $user) {
            if ($user->userId()->equals($userId)) {
                unset($this->users[$index]);
            }
        }
    }
}
