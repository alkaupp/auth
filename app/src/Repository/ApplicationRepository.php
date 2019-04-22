<?php
declare(strict_types=1);

namespace Auth\Repository;

use Auth\Entity\Application\AppId;
use Auth\Entity\Application\ClientApplication;

interface ApplicationRepository
{
    public function getById(AppId $appId): ClientApplication;
    public function store(ClientApplication $application): void;
}
