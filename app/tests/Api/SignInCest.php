<?php

// phpcs:disable PSR2.Methods.MethodDeclaration.Underscore

namespace Auth\Tests\Api;

use ApiTester;
use Auth\Entity\Application\ClientApplication;
use Auth\Entity\User\User;
use Codeception\Util\HttpCode;

class SignInCest
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
                'appId' => $appId,
            ],
            JSON_THROW_ON_ERROR,
            512
        );
    }

    public function succeedInLoginAfterRegistration(ApiTester $I): void
    {
        $app = $this->_createApp($I);
        $this->_createUser($I, $app);
        $I->sendPost(
            '/signin',
            $this->_createRequestBody(self::USERNAME, self::PASSWORD, $app->appId()->__toString())
        );
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    public function failBecauseAppNotFound(ApiTester $I): void
    {
        $app = $this->_createApp($I);
        $this->_createUser($I, $app);
        $I->sendPost(
            '/signin',
            $this->_createRequestBody(self::USERNAME, self::PASSWORD, self::INVALID_APP_ID)
        );
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    public function failBecauseUserNotFound(ApiTester $I): void
    {
        $app = $this->_createApp($I);
        $I->sendPost(
            '/signin',
            $this->_createRequestBody(self::USERNAME, self::PASSWORD, $app->appId()->__toString())
        );
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    public function failBecauseInvalidPassword(ApiTester $I): void
    {
        $app = $this->_createApp($I);
        $this->_createUser($I, $app);
        $I->sendPost(
            '/signin',
            $this->_createRequestBody(self::USERNAME, 'invalid password', $app->appId()->__toString())
        );
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }
}
