<?php

declare(strict_types=1);

namespace Auth\Tests\Unit\Controller;

use Auth\Controller\PasswordChangeAction;
use Auth\Entity\Application\Applications;
use Auth\Entity\User\BcryptPassword;
use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\User;
use Auth\Repository\InMemoryUserRepository;
use Nyholm\Psr7\ServerRequest;
use PHPStan\Testing\TestCase;

class PasswordChangeActionTest extends TestCase
{
    private const DEFAULT_USERNAME = 'frank@example.com';
    private const DEFAULT_PASSWORD = 'secrets';

    public function testReturns200Response(): void
    {
        $repo = new InMemoryUserRepository();
        $repo->store($this->createUser(self::DEFAULT_USERNAME, self::DEFAULT_PASSWORD));
        $action = new PasswordChangeAction($repo);
        $response = $action($this->createServerRequest());
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testReturns401Response(): void
    {
        $repo = new InMemoryUserRepository();
        $repo->store($this->createUser(self::DEFAULT_USERNAME, 'wrong old password'));
        $action = new PasswordChangeAction($repo);
        $response = $action($this->createServerRequest());
        $this->assertSame(401, $response->getStatusCode());
    }


    public function testReturns404Response(): void
    {
        $action = new PasswordChangeAction(new InMemoryUserRepository());
        $response = $action($this->createServerRequest());
        $this->assertSame(404, $response->getStatusCode());
    }

    private function createPayloadBody(): string
    {
        return json_encode(
            [
                'userName' => self::DEFAULT_USERNAME,
                'oldPassword' => self::DEFAULT_PASSWORD,
                'newPassword' => 'sterces'
            ],
            JSON_THROW_ON_ERROR,
            512
        );
    }

    private function createServerRequest(): ServerRequest
    {
        return new ServerRequest(
            'POST',
            '/changepassword',
            ['Content-Type' => 'application/json'],
            $this->createPayloadBody()
        );
    }

    private function createUser(string $username, string $password): User
    {
        return new User(
            new EmailAddress($username),
            new BcryptPassword($password),
            new Applications()
        );
    }
}
