<?php

declare(strict_types=1);

namespace Auth\Entity\Application;

use ArrayIterator;
use Iterator;
use IteratorAggregate;
use JsonSerializable;

/**
 * @implements IteratorAggregate<ClientApplication>
 */
class Applications implements IteratorAggregate, JsonSerializable
{
    /** @var ClientApplication[] */
    private array $applications;

    /**
     * @param array<ClientApplication> $applications
     */
    public function __construct(array $applications = [])
    {
        $this->addApplications($applications);
    }

    /**
     * @param array<ClientApplication> $applications
     */
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

    /**
     * @return Iterator<ClientApplication>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->applications);
    }

    /**
     * @return array<array>
     */
    public function jsonSerialize(): array
    {
        return array_map(
            fn(ClientApplication $application): array => $application->jsonSerialize(),
            $this->applications
        );
    }
}
