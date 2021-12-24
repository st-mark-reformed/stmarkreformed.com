<?php

declare(strict_types=1);

namespace App\Http\Response\LogIn;

class LogInPayload
{
    public function __construct(
        private bool $succeeded,
        private string $message = '',
    ) {
    }

    public function succeeded(): bool
    {
        return $this->succeeded;
    }

    public function message(): string
    {
        return $this->message;
    }
}
