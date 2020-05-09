<?php

declare(strict_types=1);

namespace Auth\Entity\Application;

interface AuthenticationToken
{
    public function toStringValue(): string;
}
