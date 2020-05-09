<?php

declare(strict_types=1);

namespace Auth\Entity\Application;

use ArrayIterator;
use Iterator;
use IteratorAggregate;
use JsonSerializable;

class Applications implements IteratorAggregate, JsonSerializable
{
    /** @var ClientApplication[] */
    private array $applications;

    public function __construct(array $applications = [])
    {
        $this->addApplications($applications);
    }

    private function addApplications(array $applications): void
    {
        foreach ($applications as $application) {
            $this->add($application);
        }
    }

    public function add(ClientApplication $application): void
    {
        $this->applications[] = $application;
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->applications);
    }

    public function jsonSerialize(): array
    {
        return array_map(
            function (ClientApplication $application): array {
                return $application->jsonSerialize();
            },
            $this->applications
        );
    }
}
