<?php
declare(strict_types=1);

return [
    ["POST", "/signin", \Auth\Controller\SignInAction::class],
    ["POST", "/register", \Auth\Controller\RegisterAction::class]
];