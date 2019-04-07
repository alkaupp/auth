<?php
declare(strict_types=1);

namespace Auth\Configuration;

use Symfony\Component\Dotenv\Dotenv;

class EnvironmentConfiguration implements Configuration
{
    private $dotEnvPath;

    public function __construct(string $dotEnvPath)
    {
        $this->dotEnvPath = $dotEnvPath;
    }

    public function configure(): void
    {
        $dotEnv = new Dotenv();
        $dotEnv->load($this->dotEnvPath);
    }
}
