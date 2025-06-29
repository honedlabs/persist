<?php

declare(strict_types=1);

namespace Honed\Persist\Drivers;

use Honed\Persist\PersistData;
use Illuminate\Support\Arr;

abstract class Driver
{
    /**
     * The name of the driver.
     */
    protected string $name;

    /**
     * The data to persist.
     *
     * @var array<string,array<string,mixed>>
     */
    protected array $data = [];

    /**
     * The resolved data from the driver.
     *
     * @var array<string,array<string,mixed>>
     */
    protected array $resolved = [];

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
    abstract public function value(string $scope): array;

    /**
     * Persist the current data to the driver.
     */
    abstract public function persist(string $scope): void;

    /**
     * Get the name of the driver.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get a value from the resolved data.
     *
     * @return array<string,mixed>
     */
    public function get(string $scope, ?string $key = null): mixed
    {
        if (! isset($this->resolved[$scope])) {
            $this->resolve($scope);
        }

        /** @var array<string,mixed> */
        return match (true) {
            $key !== null => Arr::get($this->resolved[$scope] ?? [], $key, []),
            default => $this->resolved[$scope] ?? [],
        };
    }

    /**
     * Put the value for the given key in to an internal data driver in preparation
     * to persist it.
     *
     * @param  string|array<string,mixed>  $key
     * @param  ($key is array ? array<string,mixed> : mixed)  $value
     * @return $this
     */
    public function put(string $scope, string|array|PersistData $key, mixed $value = null): self
    {
        if (is_array($key)) {
            $this->data[$scope] = [...Arr::wrap($this->data[$scope] ?? []), ...$key];
        } elseif ($key instanceof PersistData) {
            $this->data[$scope] = [...Arr::wrap($this->data[$scope] ?? []), ...$key->toArray()];
        } else {
            $this->data[$scope] ??= [];

            $value = $value instanceof PersistData ? $value->toArray() : $value;

            $this->data[$scope][$key] = $value;
        }

        return $this;
    }

    /**
     * Resolve the data from the driver.
     */
    public function resolve(string $scope): void
    {
        $this->resolved[$scope] = $this->value($scope);
    }
}
