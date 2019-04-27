<?php
declare(strict_types=1);

namespace Auth\Entity\Application;

use JsonSerializable;

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

    public function site(): string
    {
        return $this->site;
    }

    public function secret(): string
    {
        return $this->secretKey;
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
}
