<?php
declare(strict_types=1);

use function DI\create;
use function DI\get;
use Auth\Controller\DownloadImageAction;
use Auth\Storage\FileSystemStorage;
use Auth\Storage\Storage;

return [
    \League\Route\Strategy\ApplicationStrategy::class => create(\League\Route\Strategy\ApplicationStrategy::class),
    \League\Route\Router::class => create(\League\Route\Router::class)
        ->method('setStrategy', get(\League\Route\Strategy\ApplicationStrategy::class)),
    \Auth\Configuration\RouteConfiguration::class => create(\Auth\Configuration\RouteConfiguration::class)
        ->constructor(__DIR__ . '/routes.php', get(\League\Route\Router::class)),
    \Nyholm\Psr7Server\ServerRequestCreatorInterface::class => create(\Nyholm\Psr7Server\ServerRequestCreator::class)
        ->constructor(
            new \Nyholm\Psr7\Factory\Psr17Factory(),
            new \Nyholm\Psr7\Factory\Psr17Factory(),
            new \Nyholm\Psr7\Factory\Psr17Factory(),
            new \Nyholm\Psr7\Factory\Psr17Factory()
        ),
    \Auth\Configuration\EnvironmentConfiguration::class => create(\Auth\Configuration\EnvironmentConfiguration::class)
        ->constructor(__DIR__ . "/../.env"),
];