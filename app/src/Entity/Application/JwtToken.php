<?php

declare(strict_types=1);

namespace Auth\Entity\Application;

use Lcobucci\JWT\Token;

class JwtToken implements AuthenticationToken
{
    private Token $token;

    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    public function toStringValue(): string
    {
        return $this->token->__toString();
    }
}
