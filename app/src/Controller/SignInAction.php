<?php

declare(strict_types=1);

namespace Auth\Controller;

use Auth\AuthenticationException;
use Auth\AuthorizationException;
use Auth\Repository\ApplicationRepository;
use Auth\Repository\NotFoundException;
use Auth\Repository\UserRepository;
use Auth\Service\SignIn;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function json_decode;
use function trim;

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
            $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
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
