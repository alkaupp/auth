<?php
declare(strict_types=1);

namespace Auth\Controller;

use Auth\RegisterException;
use Auth\Repository\UserRepository;
use Auth\Service\Register;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RegisterAction
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
            $body = json_decode($request->getBody()->__toString(), true);
            $email = $body["userName"];
            $password = $body["password"];
            $register = new Register($this->userRepository);
            $register($email, $password);
            return new Response(200);
        } catch (\InvalidArgumentException $exception) {
            return new Response(400);
        } catch (RegisterException $exception) {
            return new Response(409);
        }
    }
}
