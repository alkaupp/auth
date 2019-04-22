<?php
declare(strict_types=1);

namespace Auth\Repository;

use Auth\Entity\Application\AppId;
use Auth\Entity\Application\ClientApplication;

class InMemoryApplicationRepository implements ApplicationRepository
{
    /** @var array|ClientApplication[] */
    private $apps;

    public function __construct()
    {
        $this->apps = [];
    }

    public function getById(AppId $appId): ClientApplication
    {
        foreach ($this->apps as $app) {
            if ($app->appId()->equals($appId)) {
                return $app;
            }
        }
        throw new NotFoundException("Application not found with id {$appId->__toString()}");
    }

    public function store(ClientApplication $application): void
    {
        $this->apps[] = $application;
    }
}
