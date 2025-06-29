<?php

declare(strict_types=1);

use Honed\Persist\Drivers\CookieDriver;
use Honed\Persist\Drivers\Decorator;
use Honed\Persist\Drivers\SessionDriver;
use Honed\Persist\Facades\Persist;
use Workbench\App\Classes\Component;

beforeEach(function () {
    $this->component = Component::make();
});

it('sets persist key', function () {
    expect($this->component)
        ->getPersistKey()->toBe('component')
        ->persistKey('test')
        ->getPersistKey()->toBe('test');
});

it('has default driver', function () {
    expect($this->component)
        ->getDefaultDriver()->toBe(Persist::getDefaultDriver())
        ->persistIn('database')->toBe($this->component)
        ->getDefaultDriver()->toBe('database');
});

it('sets persists', function () {
    expect($this->component)
        ->persistables()->toEqual(['sortQuery', 'search', 'other'])
        ->isPersistable('sortQuery')->toBeTrue()
        ->isPersistable('test')->toBeFalse();
});

it('sets lifetime', function () {
    expect($this->component)
        ->lifetime(100)->toBe($this->component)
        ->getDriver('cookie')
        ->scoped(fn ($driver) => $driver
            ->toBeInstanceOf(Decorator::class)
            ->getDriver()
            ->scoped(fn ($driver) => $driver
                ->toBeInstanceOf(CookieDriver::class)
                ->getLifetime()->toBe(100)
            )
        );
});

it('gets driver', function () {
    expect($this->component)
        ->getDriver('cookie')
        ->scoped(fn ($driver) => $driver
            ->toBeInstanceOf(Decorator::class)
            ->getDriver()->toBeInstanceOf(CookieDriver::class)
        )
        ->getDriver('session')
        ->scoped(fn ($driver) => $driver
            ->toBeInstanceOf(Decorator::class)
            ->getDriver()->toBeInstanceOf(SessionDriver::class)
        );
});

it('does not get a driver', function () {
    expect($this->component)
        ->getDriver(false)->toBeNull();
});

it('gets driver for key', function () {
    expect($this->component)
        ->getDriverFor('sortQuery')->toBeNull()
        ->setDriver('sortQuery', 'cookie')->toBe($this->component)
        ->getDriverFor('sortQuery')
        ->scoped(fn ($driver) => $driver
            ->toBeInstanceOf(Decorator::class)
            ->getDriver()->toBeInstanceOf(CookieDriver::class)
        );
});

describe('calls', function () {
    beforeEach(function () {
        $this->component->setDriver('sortQuery', true);
        $this->component->setDriver('search', true);
    });

    it('fails setting driver', function (string $method, array $parameters = []) {
        $this->component->{$method}(...$parameters);
    })->throws(BadMethodCallException::class)
        ->with([
            'non existent method' => ['missingTest'],
            'non existent key' => ['persistTest', [true]],
        ]);

    it('fails getting driver', function (string $method, array $parameters = []) {
        $this->component->{$method}(...$parameters);
    })->throws(BadMethodCallException::class)
        ->with([
            'non existent method' => ['missingTest'],
            'non existent key' => ['getMissingDriver'],
        ]);

    it('fails checking if persisting', function (string $method, array $parameters = []) {
        $this->component->{$method}(...$parameters);
    })->throws(BadMethodCallException::class)
        ->with([
            'non existent method' => ['missingTest'],
            'non existent key' => ['isPersistingTest'],
        ]);

    it('passes setting driver', function (string $method, string $key, string $store, array $parameters = []) {
        expect($this->component->{$method}(...$parameters))
            ->toBe($this->component)
            ->getDriverFor($key)
            ->scoped(fn ($driver) => $driver
                ->toBeInstanceOf(Decorator::class)
                ->getDriver()->toBeInstanceOf($store)
            );
    })->with([
        'set sort driver' => ['persistSortQuery', 'sortQuery', SessionDriver::class, [true]],
        'set search driver' => ['persistSearch', 'search', SessionDriver::class, [true]],
        'explicitly sets sort driver' => ['persistSortQueryInCookie', 'sortQuery', CookieDriver::class, [true]],
        'explicitly sets search driver' => ['persistSearchInSession', 'search', SessionDriver::class, [true]],
    ]);

    it('passes getting driver', function (string $method, array $parameters = []) {
        expect($this->component->{$method}(...$parameters))
            ->toBeInstanceOf(Decorator::class);
    })->with([
        'get sort driver' => ['getSortQueryDriver'],
        'get search driver' => ['getSearchDriver'],
    ]);

    it('passes checking if key is persisting', function (string $method, bool $outcome = true) {
        expect($this->component->{$method}())
            ->toBe($outcome);
    })->with([
        'check if sort query is persisting' => ['isPersistingSortQuery', true],
        'check if search is persisting' => ['isPersistingSearch', true],
        'check if other is persisting' => ['isPersistingOther', false],
    ]);
});
