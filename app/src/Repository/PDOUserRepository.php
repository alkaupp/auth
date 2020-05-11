<?php

declare(strict_types=1);

namespace Auth\Repository;

use Auth\Entity\User\EmailAddress;
use Auth\Entity\User\User;
use Auth\Entity\User\UserId;
use PDO;
use PDOException;

class PDOUserRepository implements UserRepository
{
    private PDO $pdo;

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
        try {
            $this->pdo->beginTransaction();
            $this->storeUser($user);
            $this->createUserApplicationMaps($user);
            $this->pdo->commit();
        } catch (PDOException $exception) {
            $this->pdo->rollBack();
            throw new PersistingException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function storeUser(User $user): void
    {
        $sql = <<<SQL
INSERT INTO "user" (id, email, password) VALUES(:id, :email, :password)
ON CONFLICT (id) DO UPDATE SET email=:email, password=:password;
SQL;
        $statement = $this->pdo->prepare($sql);
        $userArray = $user->toArray();
        unset($userArray['applications']);
        $statement->execute($userArray);
    }

    private function createUserApplicationMaps(User $user): void
    {
        $userArray = $user->toArray();
        foreach ($userArray['applications'] as $application) {
            $sql = <<<SQL
INSERT INTO user_applications (user_id, application_id) VALUES (:userId, :appId) ON CONFLICT DO NOTHING;
SQL;
            $statement = $this->pdo->prepare($sql);
            $statement->execute(['userId' => (string) $user->userId(), 'appId' => $application['appId']]);
        }
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
     * @param array<string, mixed> $parameters
     * @return User
     * @throws NotFoundException
     */
    private function getWhere(string $whereClause, array $parameters): User
    {
        $sql = 'SELECT * FROM "user"';
        $sql .= sprintf(' WHERE %s;', $whereClause);
        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC) ?: [];
        if (count($result) > 0) {
            $user = $result[0];
            $user['applications'] = $this->getApplicationsFor(UserId::fromString($user['id']));
            return User::fromArray($user);
        }
        throw new NotFoundException('User not found with');
    }

    /**
     * @param UserId $userId
     * @return array<array{'app_id': string, 'app_name': string, 'app_secretkey': string}>
     */
    private function getApplicationsFor(UserId $userId): array
    {
        $sql = <<<SQL
SELECT application.id as app_id,
   application.name as app_name,
   application.site as app_siteurl,
   application.secretkey as app_secretkey
FROM application
   JOIN user_applications ON user_applications.application_id = application.id
   WHERE user_applications.user_id = :userId;
SQL;
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['userId' => $userId->__toString()]);
        return $statement->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function remove(UserId $userId): void
    {
        $sql = 'DELETE FROM "user" WHERE id=:userId;';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['userId' => $userId->__toString()]);
    }
}
