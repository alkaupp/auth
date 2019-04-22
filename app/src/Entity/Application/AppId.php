<?php
declare(strict_types=1);

namespace Auth\Entity\Application;

use Ramsey\Uuid\Uuid;

class AppId
{
    private $value;

    public function __construct()
    {
        $this->value = Uuid::uuid4();
    }

    public static function fromString(string $uuid): AppId
    {
        $userId = new self();
        $userId->value = Uuid::fromString($uuid);
        return $userId;
    }

    public function __toString(): string
    {
        return $this->value->toString();
    }

    public function equals(AppId $appId): bool
    {
        return $this->value->equals($appId->value);
    }
}
