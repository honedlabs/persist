<?php

declare(strict_types=1);

use Honed\Persist\Facades\Persist;
use Illuminate\Support\Facades\Session;
use Workbench\App\Persists\Data\SearchData;

beforeEach(function () {
    $this->driver = Persist::driver(config('persist.drivers.session.driver'));

    $this->scope = 'component';

    Session::put($this->scope, (new SearchData('test', ['id', 'name']))->toArray());
});

it('has a name', function () {
    expect($this->driver)
        ->getName()->toEqual(config('persist.drivers.session.driver'));
});

it('gets value from store', function () {
    expect($this->driver)
        ->get($this->scope)->toEqual([
            'term' => 'test',
            'cols' => 'id,name',
        ])
        ->get($this->scope, 'term')->toBe('test');
});

it('puts key value into store', function () {
    expect($this->driver)
        ->put($this->scope, 'term', 'test');

    $this->driver->persist($this->scope);

    expect(Session::get($this->scope))
        ->toEqual([
            'component' => [
                'term' => 'test',
            ],
        ]);
});

it('puts array into store', function () {
    expect($this->driver)
        ->put($this->scope, ['term' => 'test']);

    $this->driver->persist($this->scope);

    expect(Session::get($this->scope))
        ->toEqual([
            'component' => [
                'term' => 'test',
            ],
        ]);
});

it('puts persist data into store', function () {
    expect($this->driver)
        ->put($this->scope, new SearchData('test', ['id', 'name']));

    $this->driver->persist($this->scope);

    expect(Session::get($this->scope))
        ->toEqual([
            'component' => [
                'term' => 'test',
                'cols' => 'id,name',
            ],
        ]);
});
