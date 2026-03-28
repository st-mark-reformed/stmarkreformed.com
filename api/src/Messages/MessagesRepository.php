<?php

declare(strict_types=1);

namespace App\Messages;

use App\Messages\Persistence\CreateMessage;
use App\Result\Result;

readonly class MessagesRepository
{
    public function __construct(
        private CreateMessage $createMessage,
    ) {
    }

    public function create(NewMessage $message): Result
    {
        return $this->createMessage->create(message: $message);
    }
}
