<?php

declare(strict_types=1);

namespace Honed\Persist\Drivers;

use Honed\Persist\Contracts\AccessesSession;
use Illuminate\Contracts\Session\Session;
use Illuminate\Session\SessionManager;

class SessionDriver extends Driver implements AccessesSession
{
    /**
     * The session manager to use for the driver.
     */
    protected SessionManager $session;

    public function __construct(
        string $name,
        SessionManager $session,
    ) {
        parent::__construct($name);

        $this->session = $session;
    }

    /**
     * Retrieve the data from the driver and put it in memory.
     *
     * @return array<string,mixed>
     */
    public function get(string $scope): array
    {
        /** @var array<string,mixed>|null $data */
        $data = $this->session->get($scope, []);

        return $data ?? [];
    }

    /**
     * Persist the data to the session.
     *
     * @param  array<string,mixed>  $value
     */
    public function put(string $scope, array $value): void
    {
        match (true) {
            empty($value) => $this->session->forget($scope),
            default => $this->session->put($scope, $value),
        };
    }

    /**
     * Set the session to use for the driver.
     *
     * @return $this
     */
    public function session(SessionManager $session): static
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get the session being used by the driver.
     */
    public function getSession(): SessionManager
    {
        return $this->session;
    }
}
