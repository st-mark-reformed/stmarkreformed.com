<?php

declare(strict_types=1);

namespace App\Messages;

use App\Messages\Message\Message;
use App\Messages\Persistence\CreateAndPersistFactory;
use App\Persistence\Result;

readonly class MessageRepository
{
    public function __construct(
        private CreateAndPersistFactory $createAndPersistFactory,
    ) {
    }

    public function createAndPersist(Message $message): Result
    {
        return $this->createAndPersistFactory->create($message);
    }
}
