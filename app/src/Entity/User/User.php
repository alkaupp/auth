<?php
declare(strict_types=1);

namespace Auth\Entity\User;

use Auth\AuthenticationException;
use Auth\Entity\Application\AppId;
use Auth\Entity\Application\ClientApplication;

final class User
{
    private $userId;
    private $emailAddress;
    private $password;
    private $application;

    public function __construct(EmailAddress $emailAddress, Password $password, ClientApplication $application)
    {
        $this->userId = new UserId();
        $this->emailAddress = $emailAddress;
        $this->password = $password;
        $this->application = $application;
    }

    public static function fromArray(array $user): self
    {
        $newUser = new self(
            new EmailAddress($user['email']),
            BcryptPassword::fromHash($user['password']),
            new ClientApplication(
                AppId::fromString($user['app_id']),
                $user['app_name'],
                $user['app_siteurl'],
                $user['app_secretkey'],
            )
        );
        $newUser->userId = UserId::fromString($user['id']);
        return $newUser;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function email(): EmailAddress
    {
        return $this->emailAddress;
    }

    public function application(): ClientApplication
    {
        return $this->application;
    }

    public function authenticate(string $password): void
    {
        if (!$this->password->matches($password)) {
            throw new AuthenticationException('Invalid password');
        }
    }

    public function equals(User $user): bool
    {
        return $this->emailAddress->equals($user->emailAddress);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->userId->__toString(),
            'email' => $this->email()->__toString(),
            'password' => $this->password->toHash(),
            'appId' => $this->application->appId()->__toString()
        ];
    }
}
