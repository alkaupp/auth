<?php
declare(strict_types=1);

namespace Auth\Entity\User;

use Auth\AuthenticationException;
use Auth\Entity\Application\AppId;
use Auth\Entity\Application\Applications;
use Auth\Entity\Application\AuthenticationToken;
use Auth\Entity\Application\ClientApplication;
use Auth\AuthorizationException;

final class User
{
    private $userId;
    private $emailAddress;
    private $password;
    private $applications;
    private $authenticated;

    public function __construct(EmailAddress $emailAddress, Password $password, Applications $applications)
    {
        $this->userId = new UserId();
        $this->emailAddress = $emailAddress;
        $this->password = $password;
        $this->applications = $applications;
        $this->authenticated = false;
    }

    public static function fromArray(array $user): self
    {
        $applications = array_map(
            function (array $application): ClientApplication {
                return new ClientApplication(
                    AppId::fromString($application['app_id']),
                    $application['app_name'],
                    $application['app_siteurl'],
                    $application['app_secretkey'],
                );
            },
            $user['applications']
        );
        $newUser = new self(
            new EmailAddress($user['email']),
            BcryptPassword::fromHash($user['password']),
            new Applications($applications)
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

    public function verifyPassword(string $password): void
    {
        if (!$this->password->matches($password)) {
            throw new AuthenticationException('Invalid password');
        }
        $this->authenticated = true;
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
            'applications' => $this->applications->jsonSerialize()
        ];
    }
    public function addApplication(ClientApplication $application): void
    {
        $this->applications->add($application);
    }

    public function hasApplication(ClientApplication $clientApp): bool
    {
        /** @var ClientApplication $application */
        foreach ($this->applications as $application) {
            if ($application->equals($clientApp)) {
                return true;
            }
        }
        return false;
    }

    public function isAuthenticated(): bool
    {
        return $this->authenticated;
    }
}
