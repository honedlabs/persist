<?php

declare(strict_types=1);

use Honed\Persist\Exceptions\DriverDataIntegrityException;
use Workbench\App\Persists\Data\SearchData;

beforeEach(function () {
    $this->data = SearchData::make([
        'term' => 'test',
        'cols' => ['id', 'name'],
    ]);
});

it('has array representation', function () {
    $data = SearchData::make([
        'term' => 'test',
        'cols' => ['id', 'name'],
    ]);

    expect($data)
        ->toArray()->toEqual([
            'term' => 'test',
            'cols' => 'id,name',
        ])->jsonSerialize()->toEqual($data->toArray());
});

it('validates data', function () {
    $data = SearchData::make('string');
})->throws(DriverDataIntegrityException::class);
