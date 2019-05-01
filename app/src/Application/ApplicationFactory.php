<?php
declare(strict_types=1);

namespace Auth\Application;

use Auth\Configuration\AppConfiguration;
use League\Route\Router;
use Nyholm\Psr7Server\ServerRequestCreatorInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

class ApplicationFactory
{
    private $container;
    private $isConfigured = false;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function createHttpApplication(): HttpApplication
    {
        $this->configureApplication();
        $router = $this->container->get(Router::class);
        $requestCreator = $this->container->get(ServerRequestCreatorInterface::class);
        return new HttpApplication($router, $requestCreator);
    }

    public function createConsoleApplication(): ConsoleApplication
    {
        $this->configureApplication();
        return new ConsoleApplication(new Application());
    }

    private function configureApplication(): void
    {
        if (!$this->isConfigured) {
            $appConfig = new AppConfiguration($this->container);
            $appConfig->configure();
            $this->isConfigured = true;
        }
    }
}
