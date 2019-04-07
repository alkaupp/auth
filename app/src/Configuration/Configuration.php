<?php
declare(strict_types=1);

namespace Auth\Configuration;


interface Configuration
{
    public function configure(): void;
}