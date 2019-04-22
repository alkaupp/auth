<?php
declare(strict_types=1);

namespace Auth\Repository;

use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\User;
use Auth\Entity\User\UserId;
use PDO;

class PDOUserRepository implements UserRepository
{
    /** @var PDO */
    private $pdo;

    public function __construct(PDOFactory $pdoFactory)
    {
        $this->pdo = $pdoFactory->build();
    }

    public function getById(UserId $userId): User
    {
        // TODO: Implement getById() method.
    }

    /**
     * @param EmailAddress $emailAddress
     * @return User
     * @throws NotFoundException
     */
    public function getByEmailAddress(EmailAddress $emailAddress): User
    {
        // TODO: Implement getByEmailAddress() method.
    }

    public function store(User $user): void
    {
        $sql = "INSERT INTO public.user (id, email, password) VALUES(:id, :email, :password);";
        $statement = $this->pdo->prepare($sql);
        $statement->execute($user->toArray());
    }

    public function exists(User $user): bool
    {
        $sql = "SELECT * FROM public.user WHERE email=:email;";
        $statement = $this->pdo->prepare($sql);
        $statement->execute(["email" => $user->email()->__toString()]);
        return $statement->rowCount() > 0;
    }
}
