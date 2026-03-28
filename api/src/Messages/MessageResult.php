<?php

declare(strict_types=1);

namespace App\Messages;

readonly class MessageResult
{
    public bool $hasMessage;

    public Message $message;

    public function __construct(Message|null $message = null)
    {
        $this->hasMessage = $message !== null;
        $this->message    = $message ?? new Message();
    }
}
