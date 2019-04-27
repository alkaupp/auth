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
        $sql = <<<SQL
SELECT "user".*,
       application.id as app_id,
       application.name as app_name,
       application.site as app_siteurl,
       application.secretkey as app_secretkey
FROM "user"
    JOIN application ON "user".app_id = application.id
WHERE email=:email;
SQL;
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['email' => $emailAddress->__toString()]);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            $user = $result[0];
            return User::fromArray($user);
        }
        throw new NotFoundException(sprintf('User not found with email %s', (string) $emailAddress));
    }

    public function store(User $user): void
    {
        $sql = 'INSERT INTO "user" (id, email, password, app_id) VALUES(:id, :email, :password, :appId);';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($user->toArray());
    }

    public function exists(User $user): bool
    {
        $sql = 'SELECT * FROM "user" WHERE email=:email;';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['email' => $user->email()->__toString()]);
        return $statement->rowCount() > 0;
    }
}
