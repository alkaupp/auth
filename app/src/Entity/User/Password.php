<?php

declare(strict_types=1);

namespace Auth\Entity\User;

interface Password
{
    public function matches(string $password): bool;

    public function toHash(): string;

    public static function fromHash(string $hash): Password;
}
