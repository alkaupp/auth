<?php

use Auth\Application\ApplicationFactory;
use DI\ContainerBuilder;

require __DIR__ . "/vendor/autoload.php";

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . "/config/dependency-injection.php");
$appFactory = new ApplicationFactory($containerBuilder->build());
$app = $appFactory->createHttpApplication();
$app->run();

