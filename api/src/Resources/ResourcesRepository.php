<?php

declare(strict_types=1);

namespace App\Resources;

use App\Persistence\CreateUuid;
use App\Resources\Persistence\Create\CreateResourceItem;
use App\Resources\Persistence\Delete\DeleteResourceItem;
use App\Resources\Persistence\FindAll;
use App\Resources\Persistence\FindById;
use App\Resources\Persistence\Persist\PersistResourceItem;
use App\Resources\Persistence\Transformer;
use App\Result\Result;
use Ramsey\Uuid\UuidInterface;

readonly class ResourcesRepository
{
    public function __construct(
        private FindAll $findAll,
        private FindById $findById,
        private CreateUuid $createUuid,
        private Transformer $transformer,
        private CreateResourceItem $createResourceItem,
        private DeleteResourceItem $deleteResourceItem,
        private PersistResourceItem $persistResourceItem,
    ) {
    }

    public function create(NewResourceItem $resourceItem): Result
    {
        return $this->createResourceItem->create(resourceItem: $resourceItem);
    }

    public function delete(string|UuidInterface $id): Result
    {
        $resourceItemResult = $this->findById(id: $id);

        if (! $resourceItemResult->hasResourceItem) {
            return new Result();
        }

        return $this->deleteResourceItem->delete(
            resourceItem: $resourceItemResult->resourceItem,
        );
    }

    public function persist(ResourceItem $resourceItem): Result
    {
        return $this->persistResourceItem->persist(resourceItem: $resourceItem);
    }

    public function findAll(): ResourceItems
    {
        return $this->transformer->toEntities(records: $this->findAll->find());
    }

    public function findById(string|UuidInterface $id): ResourceItemResult
    {
        $record = $this->findById->find(
            id: $this->createUuid->fromStringOrInterface(id: $id),
        );

        if ($record === null) {
            return new ResourceItemResult();
        }

        $resourceItem = $this->transformer->toEntity(record: $record);

        return new ResourceItemResult(resourceItem: $resourceItem);
    }
}
