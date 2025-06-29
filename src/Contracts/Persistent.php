<?php

declare(strict_types=1);

namespace Honed\Persist\Contracts;

interface Persistent
{
    /**
     * Define the names of different persistable properties.
     *
     * @return array<int, string>
     */
    public function persistables(): array;
}
