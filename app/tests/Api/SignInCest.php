<?php

namespace Auth\Tests\Api;

use ApiTester;
use Codeception\Util\HttpCode;

class SignInCest
{
    private const USERNAME = 'nonexistent@example.com';
    private const PASSWORD = 'mypassword';

    public function _before(ApiTester $I): void
    {
    }

    public function succeedInLoginAfterRegistration(ApiTester $I): void
    {
        $app = $I->authorizeApp('Cool app', 'https://example.com', 'mySecretKey');
        $user = $I->registerUser(self::USERNAME, self::PASSWORD, $app->appId());
        $body = json_encode(
            [
                'userName' => self::USERNAME,
                'password' => self::PASSWORD,
                'appId' => $app->appId()->__toString()
            ]
        );
        $I->sendPost('/signin', $body);
        $I->seeResponseCodeIs(HttpCode::OK);
    }
}
