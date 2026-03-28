<?php

declare(strict_types=1);

namespace App\Messages;

use App\Messages\Persistence\CreateMessage;
use App\Messages\Persistence\DeleteMessage;
use App\Persistence\CreateUuid;
use App\Result\Result;
use Ramsey\Uuid\UuidInterface;

readonly class MessagesRepository
{
    public function __construct(
        private CreateUuid $createUuid,
        private CreateMessage $createMessage,
        private DeleteMessage $deleteMessage,
    ) {
    }

    public function create(NewMessage $message): Result
    {
        return $this->createMessage->create(message: $message);
    }

    public function delete(string|UuidInterface $id): Result
    {
        return $this->deleteMessage->delete(
            id: $this->createUuid->fromStringOrInterface(id: $id),
        );
    }
}
