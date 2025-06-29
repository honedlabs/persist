<?php

declare(strict_types=1);

namespace Honed\Persist\Exceptions;

use RuntimeException;
use Throwable;

class DriverDataIntegrityException extends RuntimeException
{
    public function __construct(
        string $message = 'The data integrity of the driver is compromised.',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
