<?php

declare(strict_types=1);

namespace Auth\Repository;

use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\User;
use Auth\Entity\User\UserId;

interface UserRepository
{
    /**
     * @param UserId $userId
     * @return User
     * @throws NotFoundException
     */
    public function getById(UserId $userId): User;

    /**
     * @param EmailAddress $emailAddress
     * @return User
     * @throws NotFoundException
     */
    public function getByEmailAddress(EmailAddress $emailAddress): User;

    /**
     * @param User $user
     * @throws PersistingException
     */
    public function store(User $user): void;
    public function remove(UserId $userId): void;
}
