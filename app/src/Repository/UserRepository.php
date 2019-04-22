<?php
declare(strict_types=1);

namespace Auth\Repository;

use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\UserId;
use Auth\Entity\User\User;

interface UserRepository
{
    public function getById(UserId $userId): User;

    /**
     * @param EmailAddress $emailAddress
     * @return User
     * @throws NotFoundException
     */
    public function getByEmailAddress(EmailAddress $emailAddress): User;
    public function store(User $user): void;
    public function exists(User $user): bool;
}
