<?php
declare(strict_types=1);

namespace Auth\Entity\User;

final class EmailAddress
{
    private $emailAddress;

    public function __construct(string $emailAddress)
    {
        if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(sprintf('%s is not an email address', $emailAddress));
        }
        $this->emailAddress = $emailAddress;
    }

    public function equals(EmailAddress $emailAddress): bool
    {
        return $this->emailAddress === $emailAddress->emailAddress;
    }

    public function __toString(): string
    {
        return $this->emailAddress;
    }
}
