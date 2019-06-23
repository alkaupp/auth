<?php
declare(strict_types=1);

namespace Auth\Entity\Application;

use Auth\AuthenticationException;
use Auth\AuthorizationException;
use Auth\Entity\User\User;
use JsonSerializable;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Ramsey\Uuid\Uuid;

class ClientApplication implements JsonSerializable
{
    /** @var AppId */
    private $appId;
    private $name;
    private $site;
    private $secretKey;

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
            (new Builder())->setIssuer(getenv('AUTH_DB_JWT_ISSUER'))
            ->setAudience($this->site)
            ->setId(Uuid::uuid4()->toString(), true) // Configures the id (jti claim), replicating as a header item
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration(time() + 3600)
            ->set('userName', (string) $user->email())
            ->sign(new Sha256(), $this->secretKey)
            ->getToken()
        );
    }
}
