<?php

declare(strict_types=1);

namespace Honed\Persist;

use Honed\Persist\Exceptions\DriverDataIntegrityException;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * @implements Arrayable<string, mixed>
 */
abstract class PersistData implements Arrayable, JsonSerializable
{
    /**
     * Attempt to create the object from a given value.
     */
    abstract public static function from(mixed $value): ?static;

    /**
     * Create a new store data object.
     *
     * @throws DriverDataIntegrityException
     */
    public static function make(mixed $value): static
    {
        if ($data = static::from($value)) {
            return $data;
        }

        throw new DriverDataIntegrityException();
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
