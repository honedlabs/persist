<?php

declare(strict_types=1);

namespace Honed\Persist\Contracts;

interface CanPersistData
{
    /**
     * Get the name of the key to use when persisting data.
     *
     * @return string
     */
    public function getPersistKey();
}
