<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default driver
    |--------------------------------------------------------------------------
    |
    | Here you can specify the default driver that should be used when
    | storing and resolving persisted data. Persist ships with the
    | ability to store persisted data in an in-memory array, cache, cookie,
    | database, or session.
    |
    | Supported: "array", "cache", "cookie", "database", "session"
    |
    */
    'default' => env('PERSIST_DRIVER', 'session'),

    /*
    |--------------------------------------------------------------------------
    | Persist drivers
    |--------------------------------------------------------------------------
    |
    | Here you may configure each of the drivers that should be available.
    | These drivers shall be used to resolve and persist data.
    |
    */
    'drivers' => [
        'array' => [
            'driver' => 'array',
        ],
        'cookie' => [
            'driver' => 'cookie',
        ],
        'session' => [
            'driver' => 'session',
        ],
    ],
];
