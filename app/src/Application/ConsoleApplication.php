<?php
declare(strict_types=1);

namespace Auth\Application;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class ConsoleApplication
{
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function run(Command $command): void
    {
        $this->application = new Application();
        $this->application->add($command);
        $this->application->setDefaultCommand($command->getName(), true);
        $this->application->run();
    }
}
