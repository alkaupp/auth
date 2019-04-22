<?php
declare(strict_types=1);

namespace Auth\Tests\Controller;

use Auth\Controller\RegisterAction;
use Auth\Entity\User\BcryptPassword;
use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\User;
use Auth\Repository\InMemoryUserRepository;
use Auth\Repository\UserRepository;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class RegisterActionTest extends TestCase
{
    public function registerDataProvider(): array
    {
        $password = "youneverguess";
        $email = "frank@example.com";
        $invalidEmail = "frank@example";
        $usedEmail = "sonny@example.com";
        $repository = new InMemoryUserRepository();
        $repository->store(new User(new EmailAddress($usedEmail), new BcryptPassword($password)));
        return [
            [$repository, $this->getRequestBody($email, $password), 200],
            [$repository, $this->getRequestBody($invalidEmail, $password), 400],
            [$repository, $this->getRequestBody($usedEmail, $password), 409]
        ];
    }

    /**
     * @param UserRepository $repository
     * @param string $body
     * @param int $expectedStatusCode
     * @dataProvider registerDataProvider
     */
    public function testRegisterAction(UserRepository $repository, string $body, int $expectedStatusCode): void
    {
        $registerAction = new RegisterAction($repository);
        $request = new ServerRequest('POST', '/register', ['Content-Type' => 'application/json'], (string) $body);
        $response = $registerAction($request);
        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }

    private function getRequestBody(string $userName, string $password): string
    {
        return json_encode(["userName" => $userName, "password" => $password]);
    }
}
