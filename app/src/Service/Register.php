<?php
declare(strict_types=1);

namespace Auth\Service;

use Auth\AuthenticationException;
use Auth\Entity\Application\AppId;
use Auth\Entity\Application\Applications;
use Auth\Entity\Application\ClientApplication;
use Auth\Entity\User\BcryptPassword;
use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\User;
use Auth\RegisterException;
use Auth\Repository\ApplicationRepository;
use Auth\Repository\NotFoundException;
use Auth\Repository\UserRepository;

class Register
{
    private UserRepository $userRepository;
    private ApplicationRepository $appRepository;

    public function __construct(UserRepository $userRepository, ApplicationRepository $applicationRepository)
    {
        $this->userRepository = $userRepository;
        $this->appRepository = $applicationRepository;
    }

    /**
     * @param string $userName
     * @param string $password
     * @param string $appId
     * @return User
     * @throws NotFoundException
     * @throws RegisterException
     * @throws AuthenticationException
     */
    public function __invoke(string $userName, string $password, string $appId): User
    {
        $app = $this->appRepository->getById(AppId::fromString($appId));
        try {
            return $this->addApplicationToExistingUser($userName, $password, $app);
        } catch (NotFoundException $exception) {
            $user = new User(new EmailAddress($userName), new BcryptPassword($password), new Applications([$app]));
            $this->userRepository->store($user);
            return $user;
        }
    }

    /**
     * @param string $userName
     * @param string $password
     * @param ClientApplication $app
     * @return User
     * @throws NotFoundException
     */
    private function addApplicationToExistingUser(string $userName, string $password, ClientApplication $app): User
    {
        $user = $this->userRepository->getByEmailAddress(new EmailAddress($userName));
        $user->verifyPassword($password);
        $user->addApplication($app);
        $this->userRepository->store($user);
        return $user;
    }
}
