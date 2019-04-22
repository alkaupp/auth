<?php
declare(strict_types=1);

namespace Auth\Entity\User;

use Auth\AuthenticationException;

final class User
{
    private $userId;
    private $emailAddress;
    private $password;

    public function __construct(EmailAddress $emailAddress, Password $password)
    {
        $this->userId = new UserId();
        $this->emailAddress = $emailAddress;
        $this->password = $password;
    }

    public function email(): EmailAddress
    {
        return $this->emailAddress;
    }

    public function authenticate(string $password): void
    {
        if (!$this->password->matches($password)) {
            throw new AuthenticationException("Invalid password");
        }
    }

    public function equals(User $user): bool
    {
        return $this->emailAddress->equals($user->emailAddress);
    }

    public function toArray(): array
    {
        return [
            "id" => $this->userId->__toString(),
            "email" => $this->email()->__toString(),
            "password" => $this->password->toHash()
        ];
    }
}
