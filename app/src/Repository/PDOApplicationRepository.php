<?php
declare(strict_types=1);

namespace Auth\Repository;

use Auth\Entity\Application\AppId;
use Auth\Entity\Application\ClientApplication;
use PDO;

class PDOApplicationRepository implements ApplicationRepository
{
    private $pdo;

    public function __construct(PDOFactory $factory)
    {
        $this->pdo = $factory->build();
    }

    /**
     * @param AppId $appId
     * @return ClientApplication
     * @throws NotFoundException
     */
    public function getById(AppId $appId): ClientApplication
    {
        $sql = 'SELECT * FROM application WHERE id=:id;';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['id' => $appId->__toString()]);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            $application = $result[0];
            return ClientApplication::fromArray($application);
        }
        throw new NotFoundException(sprintf('Application not found with id %s', (string) $appId));
    }

    public function store(ClientApplication $application): void
    {
        $sql = 'INSERT INTO application (id, name, site, secretkey) VALUES (:appId, :appName, :siteUrl, :secretKey);';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($application->jsonSerialize());
    }
}
