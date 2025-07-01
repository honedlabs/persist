<?php

declare(strict_types=1);

namespace Honed\Persist\Contracts;

use Illuminate\Session\SessionManager;

interface AccessesSession
{
    /**
     * Set the session to use for the driver.
     *
     * @return $this
     */
    public function session(SessionManager $session): static;

    /**
     * Get the session being used by the driver.
     */
    public function getSession(): SessionManager;
}
