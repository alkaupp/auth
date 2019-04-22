<?php
declare(strict_types=1);

namespace Auth\Controller;

use Auth\RegisterException;
use Auth\Repository\ApplicationRepository;
use Auth\Repository\UserRepository;
use Auth\Service\Register;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RegisterAction
{
    /** @var UserRepository */
    private $userRepository;

    /** @var ApplicationRepository */
    private $appRepository;

    public function __construct(UserRepository $userRepository, ApplicationRepository $appRepository)
    {
        $this->userRepository = $userRepository;
        $this->appRepository = $appRepository;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $body = json_decode($request->getBody()->__toString(), true);
            $email = $body["userName"];
            $password = $body["password"];
            $appId = $body["appId"];
            $register = new Register($this->userRepository, $this->appRepository);
            $register($email, $password, $appId);
            return new Response(200);
        } catch (\InvalidArgumentException $exception) {
            return new Response(400);
        } catch (RegisterException $exception) {
            return new Response(409);
        }
    }
}
