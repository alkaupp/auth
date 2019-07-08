<?php
declare(strict_types=1);

namespace Auth\Controller;

use Auth\AuthenticationException;
use Auth\Repository\NotFoundException;
use Auth\Repository\UserRepository;
use Auth\Service\PasswordChange;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class PasswordChangeAction
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $passwordChange = new PasswordChange($this->userRepository);
        $body = json_decode((string) $request->getBody(), true);
        try {
            $passwordChange(trim($body['userName']), trim($body['oldPassword']), $body['newPassword']);
            return new Response(200);
        } catch (AuthenticationException $exception) {
            return new Response(401);
        } catch (NotFoundException $exception) {
            return new Response(404);
        }
    }
}