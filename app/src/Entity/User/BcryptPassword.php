<?php

declare(strict_types=1);

namespace Auth\Entity\User;

use InvalidArgumentException;

use function password_hash;
use function password_verify;

use const PASSWORD_BCRYPT;

final class BcryptPassword implements Password
{
    private string $password;

    public function __construct(string $password)
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        if ($hash === false) {
            throw new InvalidArgumentException('Failed hashing the password.');
        }
        $this->password = $hash;
    }

    public function matches(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function toHash(): string
    {
        return $this->password;
    }

    public static function fromHash(string $hash): Password
    {
        $password = new self($hash);
        $password->password = $hash;
        return $password;
    }
}
