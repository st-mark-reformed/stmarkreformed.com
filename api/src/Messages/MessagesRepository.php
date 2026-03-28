<?php

declare(strict_types=1);

namespace App\Messages;

use App\Messages\Persistence\CreateMessage;
use App\Messages\Persistence\DeleteMessage;
use App\Messages\Persistence\FindAll;
use App\Messages\Persistence\FindById;
use App\Messages\Persistence\PersistMessage;
use App\Messages\Persistence\Transformer;
use App\Persistence\CreateUuid;
use App\Result\Result;
use Ramsey\Uuid\UuidInterface;

readonly class MessagesRepository
{
    public function __construct(
        private FindAll $findAll,
        private FindById $findById,
        private CreateUuid $createUuid,
        private Transformer $transformer,
        private CreateMessage $createMessage,
        private DeleteMessage $deleteMessage,
        private PersistMessage $persistMessage,
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

    public function persist(Message $message): Result
    {
        return $this->persistMessage->persist(message: $message);
    }

    public function findAll(): Messages
    {
        return $this->transformer->toEntities(records: $this->findAll->find());
    }

    public function findById(string|UuidInterface $id): MessageResult
    {
        $record = $this->findById->find(
            id: $this->createUuid->fromStringOrInterface(id: $id),
        );

        if ($record === null) {
            return new MessageResult();
        }

        $message = $this->transformer->toEntity(record: $record);

        return new MessageResult(message: $message);
    }
}
