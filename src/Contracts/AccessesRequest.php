<?php

declare(strict_types=1);

namespace Honed\Persist\Contracts;

use Illuminate\Http\Request;

interface AccessesRequest
{
    /**
     * Set the request to use for the driver.
     *
     * @return $this
     */
    public function request(Request $request): static;

    /**
     * Get the request to use for the driver.
     */
    public function getRequest(): Request;
}
