<?php

declare(strict_types=1);

use Honed\Persist\Facades\Persist;
use Illuminate\Session\SessionManager;
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

    expect(Session::get($this->scope))
        ->toEqual([
            'term' => 'test',
            'cols' => 'id,name',
        ]);
});

it('has session', function () {
    expect($this->driver)
        ->session(app(SessionManager::class))
        ->getSession()->toBe(app(SessionManager::class));
});
