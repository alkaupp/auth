<?php

declare(strict_types=1);

namespace Auth\Entity\Application;

use Auth\AuthorizationException;
use Auth\Entity\User\User;
use JsonSerializable;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Ramsey\Uuid\Uuid;

class ClientApplication implements JsonSerializable
{
    private AppId $appId;
    private string $name;
    private string $site;
    private string $secretKey;

    public function __construct(AppId $appId, string $name, string $siteUrl, string $secretKey)
    {
        $this->appId = $appId;
        $this->name = $name;
        $this->site = $siteUrl;
        $this->secretKey = $secretKey;
    }

    public function appId(): AppId
    {
        return $this->appId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function equals(ClientApplication $application): bool
    {
        return $this->appId->equals($application->appId);
    }

    public static function fromArray(array $application): self
    {
        return new self(
            AppId::fromString($application['id']),
            $application['name'],
            $application['site'],
            $application['secretkey']
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'appId' => $this->appId->__toString(),
            'appName' => $this->name,
            'siteUrl' => $this->site,
            'secretKey' => $this->secretKey
        ];
    }

    public function authenticate(User $user): AuthenticationToken
    {
        if ($user->hasApplication($this) && $user->isAuthenticated()) {
            return $this->createTokenFor($user);
        }
        throw new AuthorizationException(sprintf('User is not a user of application %s', $this->name));
    }

    public function createTokenFor(User $user): AuthenticationToken
    {
        return new JwtToken(
            (new Builder())->issuedBy((string) getenv('AUTH_DB_JWT_ISSUER'))
            ->permittedFor($this->site)
            ->identifiedBy(Uuid::uuid4()->toString(), true)
            ->issuedAt(time())
            ->canOnlyBeUsedAfter(time())
            ->expiresAt(time() + 3600)
            ->withClaim('userName', (string) $user->email())
            ->getToken(new Sha256(), new Key($this->secretKey))
        );
    }
}
