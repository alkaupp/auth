<?php
declare(strict_types=1);

namespace Auth\Application;

use Auth\Configuration\AppConfiguration;
use League\Route\Router;
use Nyholm\Psr7Server\ServerRequestCreatorInterface;
use Psr\Container\ContainerInterface;

class ApplicationFactory
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function createHttpApplication(): HttpApplication
    {
        $appConfig = new AppConfiguration($this->container);
        $appConfig->configure();
        $router = $this->container->get(Router::class);
        $requestCreator = $this->container->get(ServerRequestCreatorInterface::class);
        return new HttpApplication($router, $requestCreator);
    }
}
