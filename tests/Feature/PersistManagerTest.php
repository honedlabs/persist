<?php

declare(strict_types=1);

use Honed\Persist\Drivers\ArrayDriver;
use Honed\Persist\Drivers\CookieDriver;
use Honed\Persist\Drivers\SessionDriver;
use Honed\Persist\PersistManager;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    $this->manager = App::make(PersistManager::class);
});

it('gets driver instance', function () {
    expect($this->manager)
        ->driver()->toBeInstanceOf(SessionDriver::class)
        ->driver(config('persist.drivers.array.driver'))->toBeInstanceOf(ArrayDriver::class)
        ->driver(config('persist.drivers.cookie.driver'))->toBeInstanceOf(CookieDriver::class);
});

it('fails if unsupported driver is requested', function () {
    $this->manager->driver('unsupported');
})->throws(InvalidArgumentException::class);

it('has default driver', function () {
    expect($this->manager)
        ->getDefaultDriver()->toBe(config('persist.drivers.session.driver'))
        ->setDefaultDriver(config('persist.drivers.array.driver'))->toBeNull()
        ->getDefaultDriver()->toBe(config('persist.drivers.array.driver'));
});

it('extends and forgets drivers', function () {
    Config::set('persist.drivers.custom.driver', 'custom');

    $this->manager
        ->extend(
            'custom',
            fn (string $name, Container $container) => new ArrayDriver($name)
        );

    expect($this->manager)
        ->driver('custom')
        ->scoped(fn ($driver) => $driver
            ->toBeInstanceOf(ArrayDriver::class)
            ->getName()->toBe('custom')
        )
        ->forgetDrivers()->toBe($this->manager)
        ->driver()->toBeInstanceOf(SessionDriver::class)
        ->forgetDriver('custom')->toBe($this->manager)
        ->driver('custom')->toBeInstanceOf(ArrayDriver::class);
});

it('delegates to driver', function () {
    expect($this->manager)
        ->get('scope')->toBe([]);
});
