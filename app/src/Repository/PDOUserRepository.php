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

    /**
     * @param UserId $userId
     * @return User
     * @throws NotFoundException
     */
    public function getById(UserId $userId): User
    {
        try {
            return $this->getWhere('id=:id', ['id' => $userId->__toString()]);
        } catch (NotFoundException $exception) {
            throw new NotFoundException(sprintf('User not found with id %s', (string) $userId));
        }
    }

    /**
     * @param EmailAddress $emailAddress
     * @return User
     * @throws NotFoundException
     */
    public function getByEmailAddress(EmailAddress $emailAddress): User
    {
        try {
            return $this->getWhere('email=:email', ['email' => $emailAddress->__toString()]);
        } catch (NotFoundException $exception) {
            throw new NotFoundException(sprintf('User not found with email %s', (string) $emailAddress));
        }
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

    /**
     * @param string $whereClause
     * @param array $parameters
     * @return User
     * @throws NotFoundException
     */
    private function getWhere(string $whereClause, array $parameters): User
    {
        $sql = <<<SQL
SELECT "user".*,
       application.id as app_id,
       application.name as app_name,
       application.site as app_siteurl,
       application.secretkey as app_secretkey
FROM "user"
    JOIN application ON "user".app_id = application.id
SQL;
        $sql .= sprintf(' WHERE %s;', $whereClause);
        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            $user = $result[0];
            return User::fromArray($user);
        }
        throw new NotFoundException('User not found with');
    }
}
