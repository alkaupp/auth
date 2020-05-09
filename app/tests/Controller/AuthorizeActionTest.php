<?php

declare(strict_types=1);

namespace Auth\Tests\Controller;

use Auth\Controller\AuthorizeAction;
use Auth\Repository\InMemoryApplicationRepository;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class AuthorizeActionTest extends TestCase
{
    public function testAuthorizeReturns200WithClientAppObject(): void
    {
        $action = new AuthorizeAction(new InMemoryApplicationRepository());
        $body = [
            'appName' => 'myApp',
            'siteUrl' => 'https://example.com',
            'secretKey' => 'appSecretKey'
        ];
        $request = new ServerRequest('POST', '/authorize', ['Content-Type' => 'application/json'], json_encode($body));
        $response = $action($request);
        $this->assertSame(200, $response->getStatusCode());
        $resultBody = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('appId', $resultBody);
        unset($resultBody['appId']);
        $this->assertEquals($body, $resultBody);
    }
}
