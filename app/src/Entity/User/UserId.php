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

    public function __toString(): string
    {
        return $this->value->toString();
    }
}
