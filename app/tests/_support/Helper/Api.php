<?php
namespace Auth\Tests\Helper;

use Auth\Entity\Application\AppId;
use Auth\Entity\Application\ClientApplication;
use Auth\Entity\User\User;
use Auth\Repository\PDOApplicationRepository;
use Auth\Repository\PDOFactory;
use Auth\Repository\PDOUserRepository;
use Auth\Service\Authorize;
use Auth\Service\Register;
use Codeception\Module;

class Api extends Module
{
    public function authorizeApp(string $name, string $siteUrl, string $secretKey): ClientApplication
    {
        $authorize = new Authorize(new PDOApplicationRepository(new PDOFactory()));
        return $authorize($name, $siteUrl, $secretKey);
    }

    public function registerUser(string $username, string $password, AppId $appId): User
    {
        $register = new Register(
            new PDOUserRepository(new PDOFactory()),
            new PDOApplicationRepository(new PDOFactory())
        );
        return $register($username, $password, $appId->__toString());
    }
}
