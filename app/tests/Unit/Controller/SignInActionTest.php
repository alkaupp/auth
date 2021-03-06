<?php

declare(strict_types=1);

namespace Auth\Tests\Unit\Controller;

use Auth\Controller\SignInAction;
use Auth\Entity\Application\AppId;
use Auth\Entity\Application\Applications;
use Auth\Entity\Application\ClientApplication;
use Auth\Entity\User\BcryptPassword;
use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\User;
use Auth\Repository\ApplicationRepository;
use Auth\Repository\InMemoryApplicationRepository;
use Auth\Repository\InMemoryUserRepository;
use Auth\Repository\UserRepository;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

use function json_encode;

class SignInActionTest extends TestCase
{
    private const APP_SECRET = '53CR37';

    private UserRepository $userRepository;
    private ApplicationRepository $appRepository;
    private ClientApplication $app;

    protected function setUp(): void
    {
        $this->userRepository = new InMemoryUserRepository();
        $this->app = new ClientApplication(new AppId(), 'app', 'https://example.com', self::APP_SECRET);
        $this->appRepository = new InMemoryApplicationRepository();
        $this->appRepository->store($this->app);
    }

    public function testSignInActionReturns401Response(): void
    {
        $action = new SignInAction($this->userRepository, $this->appRepository);
        $body = json_encode(
            [
                'userName' => 'frank@example.com',
                'password' => 'secrets',
                'appId' => $this->app->appId()->__toString()
            ],
            JSON_THROW_ON_ERROR,
            512
        );
        $request = new ServerRequest('POST', '/signin', ['Content-Type' => 'application/json'], (string) $body);
        $response = $action($request);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testSignInActionReturns200Response(): void
    {
        $response = $this->makeValidRequest();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSignInActionReturnsJwtToken(): void
    {
        $response = $this->makeValidRequest();
        $token = (new Parser())->parse((string) $response->getBody());
        $this->assertEquals('frank@example.com', $token->getClaim('userName'));
        $this->assertTrue($token->verify(new Sha256(), self::APP_SECRET));
    }

    public function testSignInActionFailsVerification(): void
    {
        $response = $this->makeValidRequest();
        $token = (new Parser())->parse((string) $response->getBody());
        $this->assertFalse($token->verify(new Sha256(), "this can't be right"));
    }


    private function makeValidRequest(): ResponseInterface
    {
        $action = new SignInAction($this->userRepository, $this->appRepository);
        $this->userRepository->store(
            new User(
                new EmailAddress('frank@example.com'),
                new BcryptPassword('secrets'),
                new Applications([$this->app])
            )
        );
        $body = json_encode(
            [
                'userName' => 'frank@example.com',
                'password' => 'secrets',
                'appId' => $this->app->appId()->__toString()
            ],
            JSON_THROW_ON_ERROR,
            512
        );
        $request = new ServerRequest('POST', '/signin', ['Content-Type' => 'application/json'], $body);
        return $action($request);
    }
}
