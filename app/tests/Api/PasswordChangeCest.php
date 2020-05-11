<?php

namespace Auth\Tests\Api;

use ApiTester;
use Auth\Entity\Application\ClientApplication;
use Auth\Entity\User\User;
use Codeception\Util\HttpCode;

class PasswordChangeCest
{
    private const USERNAME = 'nonexistent@example.com';
    private const PASSWORD = 'mypassword';
    private const NEW_PASSWORD = 'newPassword';
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

    private function _createRequestBody(string $username, string $oldPassword, string $newPassword): string
    {
        return json_encode(
            [
                'userName' => $username,
                'oldPassword' => $oldPassword,
                self::NEW_PASSWORD => $newPassword
            ],
            JSON_THROW_ON_ERROR,
            512
        );
    }

    public function succeedInPasswordChange(ApiTester $I): void
    {
        $app = $this->_createApp($I);
        $this->_createUser($I, $app);
        $I->sendPOST('/changepassword', $this->_createRequestBody(self::USERNAME, self::PASSWORD, self::NEW_PASSWORD));
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    public function failBecauseInvalidOldPassword(ApiTester $I): void
    {
        $app = $this->_createApp($I);
        $this->_createUser($I, $app);
        $I->sendPOST('/changepassword', $this->_createRequestBody(self::USERNAME, 'invalid password', self::NEW_PASSWORD));
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }
    
    public function failBecauseUserNotFound(ApiTester $I): void
    {
        $app = $this->_createApp($I);
        $this->_createUser($I, $app);
        $I->sendPOST('/changepassword', $this->_createRequestBody('unknown_user@example.com', self::PASSWORD, self::NEW_PASSWORD));
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }
}
