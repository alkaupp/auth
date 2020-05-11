<?php

declare(strict_types=1);

namespace Auth\Configuration;

use HaydenPierce\ClassFinder\ClassFinder;
use League\Route\Strategy\ApplicationStrategy;
use Psr\Container\ContainerInterface;

class AppConfiguration implements Configuration
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function configure(): void
    {
        $configurations = ClassFinder::getClassesInNamespace(__NAMESPACE__);
        foreach ($configurations as $config) {
            if ($config === self::class || $config === Configuration::class) {
                continue;
            }
            $this->applyConfiguration($this->container->get($config));
        }
        $this->configureApplicationStrategy();
    }

    private function applyConfiguration(Configuration $configuration): void
    {
        $configuration->configure();
    }

    private function configureApplicationStrategy(): void
    {
        $this->container->get(ApplicationStrategy::class)->setContainer($this->container);
    }
}
