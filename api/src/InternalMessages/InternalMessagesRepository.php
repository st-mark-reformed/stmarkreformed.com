<?php

declare(strict_types=1);

namespace App\InternalMessages;

use App\InternalMessages\Persistence\Create\CreateInternalMessage;
use App\InternalMessages\Persistence\Delete\DeleteInternalMessage;
use App\InternalMessages\Persistence\FindAll;
use App\InternalMessages\Persistence\FindById;
use App\InternalMessages\Persistence\Persist\PersistInternalMessage;
use App\InternalMessages\Persistence\Transformer;
use App\Persistence\CreateUuid;
use App\Result\Result;
use Ramsey\Uuid\UuidInterface;

readonly class InternalMessagesRepository
{
    public function __construct(
        private FindAll $findAll,
        private FindById $findById,
        private CreateUuid $createUuid,
        private Transformer $transformer,
        private CreateInternalMessage $createMessage,
        private DeleteInternalMessage $deleteMessage,
        private PersistInternalMessage $persistMessage,
    ) {
    }

    public function create(NewInternalMessage $message): Result
    {
        return $this->createMessage->create(message: $message);
    }

    public function delete(string|UuidInterface $id): Result
    {
        $messageResult = $this->findById(id: $id);

        if (! $messageResult->hasMessage) {
            return new Result();
        }

        return $this->deleteMessage->delete(message: $messageResult->message);
    }

    public function persist(InternalMessage $message): Result
    {
        return $this->persistMessage->persist(message: $message);
    }

    public function findAll(): InternalMessages
    {
        return $this->transformer->toEntities(records: $this->findAll->find());
    }

    public function findById(string|UuidInterface $id): InternalMessageResult
    {
        $record = $this->findById->find(
            id: $this->createUuid->fromStringOrInterface(id: $id),
        );

        if ($record === null) {
            return new InternalMessageResult();
        }

        $message = $this->transformer->toEntity(record: $record);

        return new InternalMessageResult(message: $message);
    }
}
