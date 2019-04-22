<?php
declare(strict_types=1);

namespace Auth\Service;

use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\User;
use Auth\Repository\UserRepository;

class SignIn
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $userName
     * @param string $password
     * @return User
     * @throws \Auth\Repository\NotFoundException
     */
    public function __invoke(string $userName, string $password): User
    {
        $user = $this->userRepository->getByEmailAddress(new EmailAddress($userName));
        $user->authenticate($password);
        return $user;
    }
}
