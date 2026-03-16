<?php

declare(strict_types=1);

namespace App\User;

use App\ExceptionHandling\KnownHandleableError;
use Throwable;

class UnableToSaveSession extends KnownHandleableError
{
    public function __construct(
        string $message = 'Unable to create session.',
        int $code = 0,
        Throwable|null $previous = null,
    ) {
        parent::__construct(
            $message,
            $code,
            $previous,
        );
    }
}
