<?php
declare(strict_types=1);

namespace Auth\Entity\User;

use function password_hash;
use function password_verify;
use const PASSWORD_BCRYPT;

final class BcryptPassword implements Password
{
    private $password;

    public function __construct(string $password)
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    public function matches(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}
