<?php

declare(strict_types=1);

namespace Honed\Persist\Facades;

use Honed\Persist\PersistManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Honed\Persist\Drivers\Driver driver(?string $name = null) Get a new driver instance
 * @method static \Honed\Persist\Drivers\ArrayDriver createArrayDriver(string $name) Create an instance of the array driver
 * @method static \Honed\Persist\Drivers\CookieDriver createCookieDriver(string $name) Create an instance of the cookie driver
 * @method static \Honed\Persist\Drivers\SessionDriver createSessionDriver(string $name) Create an instance of the session driver
 * @method static string getDefaultDriver() Get the default driver name
 * @method static void setDefaultDriver(string $name) Set the default driver name
 * @method static \Honed\Persist\PersistManager forgetDriver(string|array<int, string>|null $name = null) Unset the given driver instances
 * @method static \Honed\Persist\PersistManager forgetDrivers() Forget all of the resolved driver instances
 * @method static \Honed\Persist\PersistManager extend(string $driver, \Closure $callback) Register a custom driver creator closure
 * @method static \Honed\Persist\PersistManager session(\Illuminate\Session\SessionManager $session) Set the session to use for all drivers
 * @method static \Honed\Persist\PersistManager request(\Illuminate\Http\Request $request) Set the request to use for all drivers
 * @method static array<string,mixed> get(string $scope) Retrieve the data from the driver and set it in memory for the given scope (delegated to driver)
 * @method static void put(string $scope, array<string,mixed> $value) Persist the current data to the driver (delegated to driver)
 * @method static string getName() Get the name of the driver (delegated to driver)
 *
 * @see PersistManager
 * @see \Honed\Persist\Drivers\Driver
 */
class Persist extends Facade
{
    /**
     * Get the root object behind the facade.
     *
     * @return PersistManager
     */
    public static function getFacadeRoot()
    {
        // @phpstan-ignore-next-line
        return parent::getFacadeRoot();
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PersistManager::class;
    }
}
