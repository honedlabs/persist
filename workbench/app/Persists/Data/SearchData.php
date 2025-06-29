<?php

declare(strict_types=1);

namespace Workbench\App\Persists\Data;

use Honed\Persist\PersistData;

class SearchData extends PersistData
{
    /**
     * @param  array<int,string>  $cols
     */
    public function __construct(
        public ?string $term,
        public array $cols,
    ) {}

    /**
     * Attempt to create the object from a given value.
     */
    public static function from(mixed $value): ?static
    {
        return match (true) {
            ! is_array($value) => null,
            default => new self($value['term'], $value['cols'])
        };
    }

    /**
     * Convert the object to an array.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'term' => $this->term,
            'cols' => implode(',', $this->cols),
        ];
    }
}
