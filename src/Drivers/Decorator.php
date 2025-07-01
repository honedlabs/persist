<?php

declare(strict_types=1);

namespace Honed\Persist\Drivers;

use Honed\Persist\PersistData;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Macroable;

class Decorator
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * The scope of the decorator.
     */
    protected string $scope;

    /**
     * The underlying driver.
     */
    protected Driver $driver;

    /**
     * The data to persist.
     *
     * @var array<string,mixed>
     */
    protected array $data = [];

    /**
     * The resolved data from the driver.
     *
     * @var array<string,mixed>|null
     */
    protected ?array $resolved = null;

    /**
     * Create a new decorator instance.
     */
    public function __construct(
        string $scope,
        Driver $driver,
    ) {
        $this->setScope($scope);
        $this->setDriver($driver);
    }

    /**
     * Dynamically handle macro calls.
     *
     * @param  string  $name
     * @param  array<mixed>  $parameters
     * @return mixed
     */
    public function __call($name, $parameters)
    {
        if (static::hasMacro($name)) {
            return $this->macroCall($name, $parameters);
        }

        return $this->getDriver()->{$name}(...$parameters);
    }

    /**
     * Get the scope of the decorator.
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * Set the scope of the decorator.
     */
    public function setScope(string $scope): void
    {
        $this->scope = $scope;
    }

    /**
     * Get the underlying driver.
     */
    public function getDriver(): Driver
    {
        return $this->driver;
    }

    /**
     * Set the underlying driver.
     */
    public function setDriver(Driver $driver): void
    {
        $this->driver = $driver;
    }

    /**
     * Get a value from the store.
     *
     * @return ($key is null ? array<string,mixed> : mixed)
     */
    public function get(?string $key = null): mixed
    {
        if (! isset($this->resolved)) {
            $this->resolved = $this->getDriver()->get($this->getScope());
        }

        return match (true) {
            $key !== null => Arr::get($this->resolved, $key, null),
            default => $this->resolved,
        };
    }

    /**
     * Put a value into the internal store.
     *
     * @param  string|array<string,mixed>|PersistData  $key
     */
    public function put(string|array|PersistData $key, mixed $value = null): static
    {
        $this->data = match (true) {
            is_array($key) => [...$this->data, ...$key],
            $key instanceof PersistData => [...$this->data, ...$key->toArray()],
            default => [...$this->data, $key => $value instanceof PersistData ? $value->toArray() : $value],
        };

        return $this;
    }

    /**
     * Persist the data to the driver.
     */
    public function persist(): void
    {
        $this->getDriver()->put($this->getScope(), $this->data);
    }
}
