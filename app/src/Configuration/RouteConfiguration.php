<?php
declare(strict_types=1);

namespace Auth\Configuration;

use League\Route\RouteCollectionInterface;

class RouteConfiguration implements Configuration
{
    private $routeConfigPath;
    private $router;

    public function __construct(string $routeConfigPath, RouteCollectionInterface $router)
    {
        $this->routeConfigPath = $routeConfigPath;
        $this->router = $router;
    }

    public function configure(): void
    {
        $routeConfig = require $this->routeConfigPath;
        foreach ($routeConfig as $config) {
            [$method, $path, $handler] = $config;
            $this->mapRoute($method, $path, $handler);
        }
    }

    private function mapRoute(string $method, string $path, string $handler): void
    {
        $this->router->map($method, $path, $handler);
    }
}
