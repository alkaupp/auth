<?php
declare(strict_types=1);

namespace Auth\Tests\Controller;

use Auth\Controller\RegisterAction;
use Auth\Entity\Application\AppId;
use Auth\Entity\Application\Applications;
use Auth\Entity\Application\ClientApplication;
use Auth\Entity\User\BcryptPassword;
use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\User;
use Auth\Repository\InMemoryApplicationRepository;
use Auth\Repository\InMemoryUserRepository;
use Auth\Repository\UserRepository;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class RegisterActionTest extends TestCase
{
    private const APP_ID = '54036de4-652a-11e9-8888-c5d1c66dcec3';
    private const INVALID_APP_ID = '54036de4-652a-11e9-8888-c5d1c66dcec4';

    public function registerDataProvider(): array
    {
        $password = 'youneverguess';
        $email = 'frank@example.com';
        $invalidEmail = 'frank@example';
        $usedEmail = 'sonny@example.com';
        $repository = new InMemoryUserRepository();
        $repository->store(
            new User(
                new EmailAddress($usedEmail),
                new BcryptPassword($password),
                new Applications([new ClientApplication(new AppId(), 'blaa', 'blaa', 'blaa')])
            )
        );
        return [
            [$repository, $this->getRequestBody($email, $password, self::APP_ID), 200],
            [$repository, $this->getRequestBody($invalidEmail, $password, self::APP_ID), 400],
            [$repository, $this->getRequestBody($email, $password, self::INVALID_APP_ID), 404],
            [$repository, $this->getRequestBody($usedEmail, $password, self::APP_ID), 409]
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
        $appRepository = new InMemoryApplicationRepository();
        $appRepository->store(new ClientApplication(AppId::fromString(self::APP_ID), "blaa", "blaa", "blaa"));
        $registerAction = new RegisterAction($repository, $appRepository);
        $request = new ServerRequest('POST', '/register', ['Content-Type' => 'application/json'], (string) $body);
        $response = $registerAction($request);
        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
    }

    private function getRequestBody(string $userName, string $password, string $appId): string
    {
        return json_encode(['userName' => $userName, 'password' => $password, 'appId' => $appId]);
    }
}
