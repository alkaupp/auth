<?php
declare(strict_types=1);

use Auth\Configuration\EnvironmentConfiguration;
use Auth\Configuration\RouteConfiguration;
use Auth\Controller\AuthorizeAction;
use Auth\Controller\PasswordChangeAction;
use Auth\Controller\RegisterAction;
use Auth\Controller\SignInAction;
use Auth\Repository\ApplicationRepository;
use Auth\Repository\PDOApplicationRepository;
use Auth\Repository\PDOFactory;
use Auth\Repository\PDOUserRepository;
use Auth\Repository\UserRepository;
use function DI\create;
use function DI\get;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Nyholm\Psr7Server\ServerRequestCreatorInterface;

return [
    ApplicationStrategy::class => create(ApplicationStrategy::class),
    Router::class => create(Router::class)
        ->method('setStrategy', get(ApplicationStrategy::class)),
    RouteConfiguration::class => create(RouteConfiguration::class)
        ->constructor(__DIR__ . '/routes.php', get(Router::class)),
    ServerRequestCreatorInterface::class => create(ServerRequestCreator::class)
        ->constructor(
            new Psr17Factory(),
            new Psr17Factory(),
            new Psr17Factory(),
            new Psr17Factory()
        ),
    EnvironmentConfiguration::class => create(EnvironmentConfiguration::class)
        ->constructor(__DIR__ . "/../.env"),
    ApplicationRepository::class => create(PDOApplicationRepository::class)
        ->constructor(create(PDOFactory::class)),
    PDOUserRepository::class => create(PDOUserRepository::class)
        ->constructor(create(PDOFactory::class)),
    UserRepository::class => get(PDOUserRepository::class),
    AuthorizeAction::class => create(AuthorizeAction::class)
        ->constructor(get(ApplicationRepository::class)),
    SignInAction::class => create(SignInAction::class)
        ->constructor(get(UserRepository::class), get(ApplicationRepository::class)),
    RegisterAction::class => create(RegisterAction::class)
        ->constructor(get(UserRepository::class), get(ApplicationRepository::class)),
    PasswordChangeAction::class => create(PasswordChangeAction::class)
        ->constructor(get(UserRepository::class))
];