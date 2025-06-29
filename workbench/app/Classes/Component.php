<?php

declare(strict_types=1);

namespace Workbench\App\Classes;

use Honed\Persist\Concerns\Persistable;
use Honed\Persist\Contracts\CanPersistData;
use Illuminate\Http\Request;

class Component implements CanPersistData
{
    use Persistable;

    public function __construct(
        protected Request $request
    ) {}

    /**
     * Create a new instance of the component.
     *
     * @return static
     */
    public static function make()
    {
        return resolve(static::class);
    }

    /**
     * Get the request instance.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the persistable properties.
     */
    public function persistables(): array
    {
        return [
            'sortQuery',
            'search',
            'other',
        ];
    }
}
