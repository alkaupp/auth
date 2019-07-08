<?php

namespace Auth\Tests\Api;

use ApiTester;
use Auth\Entity\Application\ClientApplication;
use Auth\Entity\User\User;
use Codeception\Util\HttpCode;

class RegisterCest
{
    private const USERNAME = 'nonexistent@example.com';
    private const PASSWORD = 'mypassword';
    private const INVALID_APP_ID = 'ae052eda-a169-11e9-8957-c8dbf22afa69';
    private const APP_NAME = 'Cool app';
    private const APP_SITE_URL = 'https://example.com';
    private const APP_SECRET = 'supersecret';

    private $apps = [];
    private $users = [];

    public function _after(ApiTester $I): void
    {
        foreach ($this->apps as $app) {
            $I->removeApp($app->appId());
        }

        foreach ($this->users as $user) {
            $I->removeUser($user->userId());
        }
    }

    private function _createApp(ApiTester $I): ClientApplication
    {
        $app = $I->authorizeApp(self::APP_NAME, self::APP_SITE_URL, self::APP_SECRET);
        $this->apps[] = $app;
        return $app;
    }

    private function _createUser(ApiTester $I, ClientApplication $application): User
    {
        $user = $I->registerUser(self::USERNAME, self::PASSWORD, $application->appId());
        $this->users[] = $user;
        return $user;
    }

    private function _createRequestBody(string $username, string $password, string $appId): string
    {
        return json_encode(
            [
                'userName' => $username,
                'password' => $password,
                'appId' => $appId
            ]
        );
    }
    public function succeedInRegistering(ApiTester $I): void
    {
        $app = $this->_createApp($I);
        $I->sendPOST('/register', $this->_createRequestBody(self::USERNAME, self::PASSWORD, $app->appId()->__toString()));
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->removeUserByUsername(self::USERNAME);
    }

    public function failBecauseInvalidPassword(ApiTester $I): void
    {
        $app = $this->_createApp($I);
        $this->_createUser($I, $app);
        $I->sendPOST('/register', $this->_createRequestBody(self::USERNAME, 'invalid password', $app->appId()->__toString()));
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    public function failBecauseNotUsingAnEmail(ApiTester $I): void
    {
        $app = $this->_createApp($I);
        $this->_createUser($I, $app);
        $I->sendPOST('/register', $this->_createRequestBody('this is not an email', self::PASSWORD, $app->appId()->__toString()));
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    public function failBecauseApplicationNotFound(ApiTester $I): void
    {
        $I->sendPOST('/register', $this->_createRequestBody(self::USERNAME, self::PASSWORD, self::INVALID_APP_ID));
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }
}
