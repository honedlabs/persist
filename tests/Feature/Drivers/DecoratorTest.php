<?php

declare(strict_types=1);

use Honed\Persist\Drivers\Decorator;
use Honed\Persist\Drivers\Driver;
use Honed\Persist\Facades\Persist;
use Illuminate\Support\Facades\Session;

beforeEach(function () {
    $this->scope = 'component';

    $this->decorator = new Decorator(
        $this->scope,
        Persist::driver(config('persist.drivers.session.driver')),
    );

    Session::put($this->scope, ['term' => 'test']);
});

it('has a scope', function () {
    expect($this->decorator)
        ->getScope()->toBe($this->scope);
});

it('has a driver', function () {
    expect($this->decorator)
        ->getDriver()->toBeInstanceOf(Driver::class);
});

it('proxies get', function () {
    expect($this->decorator)
        ->get()->toEqual(['term' => 'test'])
        ->get('term')->toBe('test');
});

it('proxies put', function () {
    expect($this->decorator)
        ->put('term', 'test')->toBe($this->decorator)
        ->persist()->toBeNull();

    expect(Session::get($this->scope))
        ->toEqual([
            $this->scope => [
                'term' => 'test',
            ],
        ]);
});

it('has macros', function () {
    Decorator::macro('test', fn () => 'test');

    expect($this->decorator)
        ->test()->toBe('test');
});
