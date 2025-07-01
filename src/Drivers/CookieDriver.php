<?php

declare(strict_types=1);

namespace Honed\Persist\Drivers;

use Illuminate\Cookie\CookieJar;
use Illuminate\Http\Request;

class CookieDriver extends Driver
{
    /**
     * The cookie jar to use for the driver.
     *
     * @var CookieJar
     */
    protected $cookieJar;

    /**
     * The request to use for the driver.
     *
     * @var Request
     */
    protected $request;

    /**
     * The default lifetime for the cookie.
     *
     * @var int
     */
    protected $lifetime = 31536000;

    /**
     * Create a new cookie driver instance.
     */
    public function __construct(
        string $name,
        CookieJar $cookieJar,
        Request $request,
    ) {
        parent::__construct($name);

        $this->cookieJar = $cookieJar;
        $this->request = $request;
    }

    /**
     * Retrieve the data from the driver and driver it in memory.
     *
     * @return array<string,mixed>
     */
    public function get(string $scope): array
    {
        /** @var array<string,mixed>|null $data */
        $data = json_decode(
            $this->request->cookie($scope, '[]'), true // @phpstan-ignore argument.type
        );

        return $data ?? [];
    }

    /**
     * Persist the data to a cookie.
     *
     * @param  array<string,mixed>  $value
     */
    public function put(string $scope, array $value): void
    {
        match (true) {
            empty($value) => $this->cookieJar->forget($scope),
            default => $this->cookieJar->queue(
                $scope, json_encode($value, JSON_THROW_ON_ERROR), $this->lifetime
            ),
        };
    }

    /**
     * Set the request to use for the driver.
     *
     * @return $this
     */
    public function request(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get the request to use for the driver.
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Set the cookie jar to use for the driver.
     *
     * @return $this
     */
    public function cookieJar(CookieJar $cookieJar): self
    {
        $this->cookieJar = $cookieJar;

        return $this;
    }

    /**
     * Get the cookie jar to use for the driver.
     */
    public function getCookieJar(): CookieJar
    {
        return $this->cookieJar;
    }

    /**
     * Set the lifetime for the cookie.
     *
     * @return $this
     */
    public function lifetime(int $seconds): self
    {
        $this->lifetime = $seconds;

        return $this;
    }

    /**
     * Get the lifetime for the cookie.
     */
    public function getLifetime(): int
    {
        return $this->lifetime;
    }
}
