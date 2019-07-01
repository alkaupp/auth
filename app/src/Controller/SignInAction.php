<?php
declare(strict_types=1);

namespace Auth\Controller;

use Auth\AuthenticationException;
use Auth\AuthorizationException;
use Auth\Entity\User\User;
use Auth\Repository\ApplicationRepository;
use Auth\Repository\NotFoundException;
use Auth\Repository\UserRepository;
use Auth\Service\SignIn;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function json_decode;
use function trim;
use function time;

use Ramsey\Uuid\Uuid;

class SignInAction
{
    private UserRepository $userRepository;
    private ApplicationRepository $appRepository;

    public function __construct(UserRepository $userRepository, ApplicationRepository $appRepository)
    {
        $this->userRepository = $userRepository;
        $this->appRepository = $appRepository;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $body = json_decode((string) $request->getBody(), true);
            $signIn = new SignIn($this->userRepository, $this->appRepository);
            $token = $signIn(trim($body['userName']), trim($body['password']), $body['appId']);
            return new Response(200, [], $token->toStringValue());
        } catch (AuthenticationException $exception) {
            return new Response(401);
        } catch (AuthorizationException $exception) {
            return new Response(401);
        } catch (NotFoundException $exception) {
            return new Response(401);
        }
    }
}
