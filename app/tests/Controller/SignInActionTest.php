<?php
declare(strict_types=1);

namespace Auth\Tests\Controller;

use Auth\Controller\SignInAction;
use Auth\Entity\Application\AppId;
use Auth\Entity\User\BcryptPassword;
use Auth\Entity\Application\ClientApplication;
use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\User;
use Auth\Repository\InMemoryUserRepository;
use Auth\Repository\UserRepository;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

use function json_encode;
use Psr\Http\Message\ResponseInterface;

class SignInActionTest extends TestCase
{
    private const APP_SECRET = '53CR37';
    /** @var UserRepository */
    private $userRepository;

    protected function setUp()
    {
        $this->userRepository = new InMemoryUserRepository();
    }

    public function testSignInActionReturns401Response(): void
    {
        $action = new SignInAction($this->userRepository);
        $body = json_encode(['userName' => 'frank@example.com', 'password' => 'secrets']);
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
        $action = new SignInAction($this->userRepository);
        $this->userRepository->store(
            new User(
                new EmailAddress('frank@example.com'),
                new BcryptPassword('secrets'),
                new ClientApplication(new AppId(), 'app', 'https://example.com', self::APP_SECRET)
            )
        );
        $body = json_encode(['userName' => 'frank@example.com', 'password' => 'secrets']);
        $request = new ServerRequest('POST', '/signin', ['Content-Type' => 'application/json'], $body);
        return $action($request);
    }
}
