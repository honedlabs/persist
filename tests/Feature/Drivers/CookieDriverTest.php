<?php

declare(strict_types=1);

use Honed\Persist\Drivers\CookieDriver;
use Honed\Persist\Facades\Persist;
use Illuminate\Cookie\CookieJar;
use Illuminate\Http\Request;
use Workbench\App\Persists\Data\SearchData;

beforeEach(function () {
    /** @var CookieDriver */
    $this->driver = Persist::driver(config('persist.drivers.cookie.driver'));

    $this->scope = 'component';

    $request = Request::create('/', 'GET', cookies: [
        $this->scope => json_encode((new SearchData('test', ['id', 'name']))),
    ]);

    $this->driver->request($request);
});

it('has a name', function () {
    expect($this->driver)
        ->getName()->toEqual(config('persist.drivers.cookie.driver'));
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

    expect(app(CookieJar::class)->getQueuedCookies())
        ->toHaveCount(1)
        ->{0}
        ->scoped(fn ($cookie) => $cookie
            ->getName()->toBe($this->scope)
            ->getValue()->toBe(json_encode([
                'term' => 'test',
                'cols' => 'id,name',
            ]))
        );
});

it('has request', function () {
    expect($this->driver)
        ->request(app(Request::class))->toBe($this->driver)
        ->getRequest()->toBe(app(Request::class));
});

it('has cookie jar', function () {
    expect($this->driver)
        ->cookieJar(app(CookieJar::class))->toBe($this->driver)
        ->getCookieJar()->toBe(app(CookieJar::class));
});

it('has lifetime', function () {
    expect($this->driver)
        ->lifetime(10)->toBe($this->driver)
        ->getLifetime()->toBe(10);
});
