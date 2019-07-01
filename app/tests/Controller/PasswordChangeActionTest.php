<?php
declare(strict_types=1);

namespace Auth\Tests\Controller;

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
        $repo->store(
            new User(
                new EmailAddress(self::DEFAULT_USERNAME),
                new BcryptPassword(self::DEFAULT_PASSWORD),
                new Applications()
            )
        );
        $action = new PasswordChangeAction($repo);
        $response = $action(
            new ServerRequest(
                'POST',
                '/changepassword',
                ['Content-Type' => 'application/json'],
                $this->createPayloadBody()
            )
        );
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testReturns401Response(): void
    {
        $repo = new InMemoryUserRepository();
        $repo->store(
            new User(
                new EmailAddress(self::DEFAULT_USERNAME),
                new BcryptPassword('wrong old password'),
                new Applications()
            )
        );
        $action = new PasswordChangeAction($repo);
        $response = $action(
            new ServerRequest(
                'POST',
                '/changepassword',
                ['Content-Type' => 'application/json'],
                $this->createPayloadBody()
            )
        );
        $this->assertSame(401, $response->getStatusCode());
    }


    public function testReturns404Response(): void
    {
        $action = new PasswordChangeAction(new InMemoryUserRepository());
        $response = $action(
            new ServerRequest(
                'POST',
                '/changepassword',
                ['Content-Type' => 'application/json'],
                $this->createPayloadBody()
            )
        );
        $this->assertSame(404, $response->getStatusCode());
    }

    private function createPayloadBody(): string
    {
        return json_encode(
            [
                'userName' => self::DEFAULT_USERNAME,
                'oldPassword' => self::DEFAULT_PASSWORD,
                'newPassword' => 'sterces'
            ]
        );
    }
}
