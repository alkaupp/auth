<?php

declare(strict_types=1);

namespace Auth\Repository;

use Auth\Entity\Application\AppId;
use Auth\Entity\Application\ClientApplication;

interface ApplicationRepository
{
    /**
     * @param AppId $appId
     * @return ClientApplication
     * @throws NotFoundException
     */
    public function getById(AppId $appId): ClientApplication;
    public function store(ClientApplication $application): void;
    public function remove(AppId $appId): void;
}
