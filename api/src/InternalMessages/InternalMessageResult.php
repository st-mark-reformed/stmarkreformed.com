<?php

declare(strict_types=1);

namespace App\InternalMessages;

readonly class InternalMessageResult
{
    public bool $hasMessage;

    public InternalMessage $message;

    public function __construct(InternalMessage|null $message = null)
    {
        $this->hasMessage = $message !== null;
        $this->message    = $message ?? new InternalMessage();
    }
}
