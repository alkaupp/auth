<?php

declare(strict_types=1);

namespace Auth\Application;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class ConsoleApplication
{
    private Application $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function run(Command $command): void
    {
        $this->application = new Application();
        $this->application->add($command);
        $this->application->setDefaultCommand((string) $command->getName(), true);
        $this->application->run();
    }
}
