<?php

declare(strict_types=1);

namespace Honed\Persist;

use Closure;
use Honed\Persist\Contracts\AccessesRequest;
use Honed\Persist\Contracts\AccessesSession;
use Honed\Persist\Drivers\ArrayDriver;
use Honed\Persist\Drivers\CookieDriver;
use Honed\Persist\Drivers\Driver;
use Honed\Persist\Drivers\SessionDriver;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Session\Session;
use Illuminate\Cookie\CookieJar;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use InvalidArgumentException;

class PersistManager
{
    /**
     * The container instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * The array of resolved persist drivers.
     *
     * @var array<string, Driver>
     */
    protected $drivers = [];

    /**
     * The registered custom driver creators.
     *
     * @var array<string, Closure(string, Container): Driver>
     */
    protected $customCreators = [];

    /**
     * The session to pass to drivers.
     *
     * @var SessionManager|null
     */
    protected $session;

    /**
     * The request to pass to drivers.
     *
     * @var Request|null
     */
    protected $request;

    /**
     * Create a new view resolver.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  array<array-key, mixed>  $parameters
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->driver()->$method(...$parameters);
    }

    /**
     * Get a new driver instance.
     *
     * @throws InvalidArgumentException
     */
    public function driver(?string $name = null): Driver
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->drivers[$name] = $this->resolve($name);
    }

    /**
     * Create an instance of the array driver.
     */
    public function createArrayDriver(string $name): ArrayDriver
    {
        return new ArrayDriver($name);
    }

    /**
     * Create an instance of the database driver.
     */
    public function createCookieDriver(string $name): CookieDriver
    {
        return new CookieDriver(
            $name, $this->getCookieJar(), $this->getRequest()
        );
    }

    /**
     * Create an instance of the database driver.
     */
    public function createSessionDriver(string $name): SessionDriver
    {
        return new SessionDriver(
            $name, $this->getSession()
        );
    }

    /**
     * Set the session to use for all drivers.
     *
     * @return $this
     */
    public function session(SessionManager $session): static
    {
        $this->session = $session;

        foreach ($this->drivers as $driver) {
            if ($driver instanceof AccessesSession) {
                $driver->session($session);
            }
        }

        return $this;
    }

    /**
     * Set the request to use for all drivers.
     *
     * @return $this
     */
    public function request(Request $request): static
    {
        foreach ($this->drivers as $driver) {
            if ($driver instanceof AccessesRequest) {
                $driver->request($request);
            }
        }

        $this->request = $request;

        return $this;
    }

    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        // @phpstan-ignore-next-line offsetAccess.nonOffsetAccessible
        return $this->container['config']->get('persist.driver', 'session');
    }

    /**
     * Set the default driver name.
     */
    public function setDefaultDriver(string $name): void
    {
        // @phpstan-ignore-next-line offsetAccess.nonOffsetAccessible
        $this->container['config']->set('persist.driver', $name);
    }

    /**
     * Unset the given driver instances.
     *
     * @param  string|array<int, string>|null  $name
     * @return $this
     */
    public function forgetDriver(string|array|null $name = null): self
    {
        $name ??= $this->getDefaultDriver();

        foreach ((array) $name as $driverName) {
            if (isset($this->drivers[$driverName])) {
                unset($this->drivers[$driverName]);
            }
        }

        return $this;
    }

    /**
     * Forget all of the resolved driver instances.
     *
     * @return $this
     */
    public function forgetDrivers(): self
    {
        $this->drivers = [];

        return $this;
    }

    /**
     * Register a custom driver creator closure.
     *
     * @param  Closure(string, Container): Driver  $callback
     * @return $this
     */
    public function extend(string $driver, Closure $callback): self
    {
        $this->customCreators[$driver] = $callback->bindTo($this, $this);

        return $this;
    }

    /**
     * Resolve the given driver.
     *
     * @throws InvalidArgumentException
     */
    protected function resolve(string $name): Driver
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException(
                "Persist driver [{$name}] is not defined."
            );
        }

        if (isset($this->customCreators[$name])) {
            $driver = $this->callCustomCreator($name);
        } else {
            /** @var string */
            $driverName = $config['driver'];

            $method = 'create'.ucfirst($driverName).'Driver';

            if (method_exists($this, $method)) {
                /** @var Driver */
                $driver = $this->{$method}($name);
            } else {
                throw new InvalidArgumentException(
                    "Driver [{$name}] is not supported."
                );
            }
        }

        return $driver;
    }

    /**
     * Call a custom driver creator.
     */
    protected function callCustomCreator(string $name): Driver
    {
        return $this->customCreators[$name]($name, $this->container);
    }

    /**
     * Get the driver configuration.
     *
     * @return array<string, mixed>|null
     */
    protected function getConfig(string $name): ?array
    {
        /** @var array<string, mixed>|null */
        return $this->container['config']["persist.drivers.{$name}"]; // @phpstan-ignore-line offsetAccess.nonOffsetAccessible
    }

    /**
     * Get the database manager instance from the container.
     */
    protected function getDatabaseManager(): DatabaseManager
    {
        /** @var DatabaseManager */
        return $this->container['db']; // @phpstan-ignore-line offsetAccess.nonOffsetAccessible
    }

    /**
     * Get the event dispatcher instance from the container.
     */
    protected function getDispatcher(): Dispatcher
    {
        /** @var Dispatcher */
        return $this->container['events']; // @phpstan-ignore-line offsetAccess.nonOffsetAccessible
    }

    /**
     * Get the cookie jar instance from the container.
     */
    protected function getCookieJar(): CookieJar
    {
        /** @var CookieJar */
        return $this->container['cookie']; // @phpstan-ignore-line offsetAccess.nonOffsetAccessible
    }

    /**
     * Get the request instance from the container.
     */
    protected function getRequest(): Request
    {
        /** @var Request */
        return $this->request ??
            $this->container['request']; // @phpstan-ignore-line offsetAccess.nonOffsetAccessible
    }

    /**
     * Get the session manager instance from the container.
     */
    protected function getSession(): SessionManager
    {
        /** @var SessionManager */
        return $this->session ??
            $this->container['session']; // @phpstan-ignore-line offsetAccess.nonOffsetAccessible
    }
}
