<?php

namespace Auth\Tests\Api;

use ApiTester;
use Auth\Entity\Application\AppId;
use Codeception\Util\HttpCode;

class AuthorizeCest
{
    public function authorizeSuccessfully(ApiTester $I): void
    {
        $body = json_encode(
            ['appName' => 'myApp', 'siteUrl' => 'https://example.com', 'secretKey' => 'appSecretKey'],
            JSON_THROW_ON_ERROR,
            512
        );
        $I->sendPOST('/authorize', $body);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $response = json_decode($I->grabResponse(), true, 512, JSON_THROW_ON_ERROR);
        $appId = AppId::fromString($response['appId']);
        $I->removeApp($appId);
    }
}
