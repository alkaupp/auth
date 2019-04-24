<?php
declare(strict_types=1);

namespace Auth\Service;

use Auth\Entity\Application\AppId;
use Auth\Entity\Application\ClientApplication;
use Auth\Repository\ApplicationRepository;

class Authorize
{
    /** @var ApplicationRepository */
    private $appRepository;

    public function __construct(ApplicationRepository $appRepository)
    {
        $this->appRepository = $appRepository;
    }

    public function __invoke(string $name, string $siteUrl, string $secretKey): ClientApplication
    {
        $app = new ClientApplication(new AppId(), $name, $siteUrl, $secretKey);
        $this->appRepository->store($app);
        return $app;
    }
}
