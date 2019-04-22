<?php
declare(strict_types=1);

namespace Auth\Entity\User;

class ClientApplication
{
    private $name;
    private $secretKey;

    public function __construct(string $name, string $secretKey)
    {
        $this->name = $name;
        $this->secretKey = $secretKey;
    }
}
