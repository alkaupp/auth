<?php
declare(strict_types=1);

namespace Auth\Service;

use Auth\Entity\Application\AppId;
use Auth\Entity\Application\Applications;
use Auth\Entity\User\BcryptPassword;
use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\User;
use Auth\RegisterException;
use Auth\Repository\ApplicationRepository;
use Auth\Repository\NotFoundException;
use Auth\Repository\UserRepository;

class Register
{
    /** @var UserRepository */
    private $userRepository;

    /** @var ApplicationRepository */
    private $appRepository;

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
     */
    public function __invoke(string $userName, string $password, string $appId): User
    {
        $app = $this->appRepository->getById(AppId::fromString($appId));
        $user = new User(new EmailAddress($userName), new BcryptPassword($password), new Applications([$app]));
        if ($this->userRepository->exists($user)) {
            throw new RegisterException('Username is already taken.');
        }
        $this->userRepository->store($user);
        return $user;
    }
}
