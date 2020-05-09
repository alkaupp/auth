<?php

declare(strict_types=1);

namespace Auth\Service;

use Auth\AuthenticationException;
use Auth\Entity\Application\AppId;
use Auth\Entity\Application\AuthenticationToken;
use Auth\Entity\User\EmailAddress;
use Auth\AuthorizationException;
use Auth\Repository\ApplicationRepository;
use Auth\Repository\NotFoundException;
use Auth\Repository\UserRepository;

class SignIn
{
    private UserRepository $userRepository;
    private ApplicationRepository $appRepository;

    public function __construct(UserRepository $userRepository, ApplicationRepository $appRepository)
    {
        $this->userRepository = $userRepository;
        $this->appRepository = $appRepository;
    }

    /**
     * @param string $userName
     * @param string $password
     * @param string $appId
     * @return AuthenticationToken
     * @throws NotFoundException
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    public function __invoke(string $userName, string $password, string $appId): AuthenticationToken
    {
        $app = $this->appRepository->getById(AppId::fromString($appId));
        $user = $this->userRepository->getByEmailAddress(new EmailAddress($userName));
        $user->verifyPassword($password);
        return $app->authenticate($user);
    }
}
