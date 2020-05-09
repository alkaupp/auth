<?php

declare(strict_types=1);

namespace Auth\Tests\Api;

use ApiTester;

final class FrontpageCest
{
    public function testFrontpageRedirectsToGithub(ApiTester $I): void
    {
        $I->stopFollowingRedirects();
        $I->sendGET('/');
        $I->seeHttpHeader('Location', 'https://github.com/alkaupp/auth');
        $I->seeResponseCodeIsRedirection();
    }
}
