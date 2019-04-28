<?php
declare(strict_types=1);

namespace Auth\Entity\User;

use Ramsey\Uuid\Uuid;

class UserId
{
    private $value;

    public function __construct()
    {
        $this->value = Uuid::uuid4();
    }

    public static function fromString(string $uuid): UserId
    {
        $userId = new self();
        $userId->value = Uuid::fromString($uuid);
        return $userId;
    }

    public function __toString(): string
    {
        return $this->value->toString();
    }

    public function equals(UserId $userId): bool
    {
        return $this->value->equals($userId->value);
    }
}
