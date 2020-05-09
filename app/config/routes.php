<?php
declare(strict_types=1);

use Auth\Controller\AuthorizeAction;
use Auth\Controller\PasswordChangeAction;
use Auth\Controller\RegisterAction;
use Auth\Controller\SignInAction;

return [
    ['POST', '/authorize', AuthorizeAction::class],
    ['POST', '/signin', SignInAction::class],
    ['POST', '/register', RegisterAction::class],
    ['POST', '/changepassword', PasswordChangeAction::class],
];