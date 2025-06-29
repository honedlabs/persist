<?php

declare(strict_types=1);

use Honed\Persist\Facades\Persist;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Session;
use Workbench\App\Persists\Data\SearchData;

beforeEach(function () {
    $this->driver = Persist::driver(config('persist.drivers.session.driver'));

    $this->scope = 'component';

    $this->key = 'key';

    Session::put($this->scope, [
        $this->key => (new SearchData('test', ['id', 'name']))->toArray(),
    ]);
});

it('has a name', function () {
    expect($this->driver)
        ->getName()->toEqual(config('persist.drivers.session.driver'));
});

it('gets persisted value', function () {
    expect($this->driver)
        ->get($this->scope)->toEqual([
            $this->key => [
                'term' => 'test',
                'cols' => 'id,name',
            ],
        ]);
});

it('persists value into store', function () {
    expect($this->driver)
        ->put($this->scope, 'term', 'test');

    $this->driver->persist($this->scope);

    expect(Session::get($this->scope))
        ->toEqual([
            $this->scope => [
                'term' => 'test',
            ],
        ]);
});

it('sets session', function () {
    expect($this->driver)
        ->session(app(SessionManager::class))
        ->getSession()->toBe(app(SessionManager::class));
});
