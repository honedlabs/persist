<?php

declare(strict_types=1);

use Honed\Persist\Facades\Persist;
use Workbench\App\Persists\Data\SearchData;

beforeEach(function () {
    $this->driver = Persist::driver(config('persist.drivers.array.driver'));

    $this->scope = 'component';

    $this->driver->put($this->scope, (new SearchData('test', ['id', 'name']))->toArray());
});

it('has a name', function () {
    expect($this->driver)
        ->getName()->toEqual(config('persist.drivers.array.driver'));
});

it('gets value', function () {
    expect($this->driver)
        ->get($this->scope)->toEqual([
            'term' => 'test',
            'cols' => 'id,name',
        ]);
});

it('sets value', function () {
    expect($this->driver)
        ->put($this->scope, (new SearchData('test', ['id', 'name']))->toArray());

    expect($this->driver)
        ->get($this->scope)->toEqual([
            'term' => 'test',
            'cols' => 'id,name',
        ]);
});
