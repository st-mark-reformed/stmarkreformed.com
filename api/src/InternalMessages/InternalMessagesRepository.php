<?php

declare(strict_types=1);

namespace App\InternalMessages;

use App\InternalMessages\Persistence\FindAll;
use App\InternalMessages\Persistence\FindById;
use App\InternalMessages\Persistence\Transformer;
use App\Persistence\CreateUuid;
use Ramsey\Uuid\UuidInterface;

readonly class InternalMessagesRepository
{
    public function __construct(
        private FindAll $findAll,
        private FindById $findById,
        private CreateUuid $createUuid,
        private Transformer $transformer,
    ) {
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
