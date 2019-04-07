<?php
declare(strict_types=1);

namespace Auth\Controller;

use Auth\AuthenticationException;
use Auth\Entity\User\User;
use Auth\Repository\NotFoundException;
use Auth\Repository\UserRepository;
use Auth\Service\SignIn;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Token;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function json_decode;
use Ramsey\Uuid\Uuid;

class SignInAction
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $body = json_decode((string) $request->getBody(), true);
            $signIn = new SignIn($this->userRepository);
            $user = $signIn(trim($body['userName']), trim($body['password']));
            return new Response(200, ['Content-Type' => 'application/json'], (string) $this->createJwtForUser($user));
        } catch (AuthenticationException $exception) {
        } catch (NotFoundException $exception) {
            return new Response(401);
        }
    }

    private function createJwtForUser(User $user): Token
    {
        return (new Builder())->setIssuer('https://auth.aleksikauppi.la')
        ->setAudience('https://aleksikauppi.la')
        ->setId(Uuid::uuid4(), true) // Configures the id (jti claim), replicating as a header item
        ->setIssuedAt(time())
        ->setNotBefore(time() + 60)
        ->setExpiration(time() + 3600)
        ->set('userName', (string) $user->email())
        ->getToken();

    }
}
