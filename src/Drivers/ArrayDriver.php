<?php

declare(strict_types=1);

namespace Honed\Persist\Drivers;

class ArrayDriver extends Driver
{
    /**
     * The persisted data.
     *
     * @var array<string,array<string,mixed>>
     */
    protected array $persisted = [];

    /**
     * Retrieve the data from the driver and put it in memory for the given key.
     *
     * @return array<string,mixed>
     */
    public function value(string $scope): array
    {
        return $this->persisted[$scope] ?? [];
    }

    /**
     * Persist the data to the array.
     */
    public function persist(string $scope): void
    {
        /** @var array<string,mixed> $data */
        $data = $this->data[$scope] ?? [];

        $this->persisted[$scope] = $data;
    }
}
