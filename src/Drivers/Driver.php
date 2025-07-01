<?php

declare(strict_types=1);

namespace Honed\Persist\Drivers;

abstract class Driver
{
    /**
     * The name of the driver.
     */
    protected string $name;

    /**
     * Create a new instance of the driver.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Retrieve the data from the driver and set it in memory for the given key.
     *
     * @return array<string,mixed>
     */
    abstract public function get(string $scope): array;

    /**
     * Persist the current data to the driver.
     *
     * @param  array<string,mixed>  $value
     */
    abstract public function put(string $scope, array $value): void;

    /**
     * Get the name of the driver.
     */
    public function getName(): string
    {
        return $this->name;
    }
}
