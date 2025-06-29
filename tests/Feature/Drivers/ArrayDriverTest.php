<?php

declare(strict_types=1);

use Honed\Persist\Facades\Persist;
use Workbench\App\Persists\Data\SearchData;

beforeEach(function () {
    $this->driver = Persist::driver(config('persist.drivers.array.driver'));

    $this->key = 'component';

    $this->scope = 'key';

    $this->driver->put($this->key, $this->scope, new SearchData('test', ['id', 'name']));

    $this->driver->persist($this->key);
});

it('has a name', function () {
    expect($this->driver)
        ->getName()->toEqual(config('persist.drivers.array.driver'));
});

it('gets persisted value', function () {
    expect($this->driver)
        ->get($this->key)->toEqual([
            $this->scope => [
                'term' => 'test',
                'cols' => 'id,name',
            ],
        ]);
});

it('persists value into store', function () {
    expect($this->driver)
        ->put($this->key, 'term', 'test');

    $this->driver->persist($this->key);

    $this->driver->resolve($this->key);

    expect($this->driver)
        ->get($this->key)->toEqual([
            'term' => 'test',
            $this->scope => [
                'term' => 'test',
                'cols' => 'id,name',
            ],
        ]);
});
