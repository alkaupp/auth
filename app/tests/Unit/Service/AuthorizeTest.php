<?php

declare(strict_types=1);

namespace Auth\Tests\Unit\Service;

use Auth\Entity\Application\ClientApplication;
use Auth\Repository\InMemoryApplicationRepository;
use Auth\Service\Authorize;
use PHPUnit\Framework\TestCase;

class AuthorizeTest extends TestCase
{
    public function testAuthorizeReturnsClientApplication(): void
    {
        $authorize = new Authorize(new InMemoryApplicationRepository());
        $appName = 'myApp';
        $siteUrl = 'https://example.com';
        $secretKey = 'myAppSecret';
        $app = $authorize($appName, $siteUrl, $secretKey);
        $this->assertClientAppValuesMatch($app, $appName);
    }

    public function testAuthorizeStoresClientApplicationInRepository(): void
    {
        $appRepository = new InMemoryApplicationRepository();
        $authorize = new Authorize($appRepository);
        $app = $authorize('myApp', 'https://example.com', 'myAppSecret');
        $this->assertSame($app, $appRepository->getById($app->appId()));
    }

    private function assertClientAppValuesMatch(ClientApplication $app, string $appName): void
    {
        $this->assertSame($appName, $app->name());
    }
}
