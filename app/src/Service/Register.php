<?php
declare(strict_types=1);

namespace Auth\Service;

use Auth\Entity\User\BcryptPassword;
use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\User;
use Auth\RegisterException;
use Auth\Repository\UserRepository;

class Register
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(string $userName, string $password): User
    {
        $user = new User(new EmailAddress($userName), new BcryptPassword($password));
        if ($this->userRepository->exists($user)) {
            throw new RegisterException("Username is already taken.");
        }
        $this->userRepository->store($user);
        return $user;
    }
}
