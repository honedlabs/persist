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
    protected array $store = [];

    /**
     * Retrieve the data from the driver and put it in memory for the given key.
     *
     * @return array<string,mixed>
     */
    public function get(string $scope): array
    {
        return $this->store[$scope] ?? [];
    }

    /**
     * Persist the data to the array.
     *
     * @param  array<string,mixed>  $value
     */
    public function put(string $scope, array $value): void
    {
        $this->store[$scope] = $value;
    }
}
