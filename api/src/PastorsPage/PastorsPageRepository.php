<?php

declare(strict_types=1);

namespace App\PastorsPage;

use App\PastorsPage\Persistence\Create\CreatePastorsPageItem;
use App\PastorsPage\Persistence\Delete\DeletePastorsPageItem;
use App\PastorsPage\Persistence\FindAll;
use App\PastorsPage\Persistence\FindById;
use App\PastorsPage\Persistence\Persist\PersistPastorsPageItem;
use App\PastorsPage\Persistence\Transformer;
use App\Persistence\CreateUuid;
use App\Result\Result;
use Ramsey\Uuid\UuidInterface;

readonly class PastorsPageRepository
{
    public function __construct(
        private FindAll $findAll,
        private FindById $findById,
        private CreateUuid $createUuid,
        private Transformer $transformer,
        private CreatePastorsPageItem $createPastorsPageItem,
        private DeletePastorsPageItem $deletePastorsPageItem,
        private PersistPastorsPageItem $persistPastorsPageItem,
    ) {
    }

    public function create(NewPastorsPageItem $pastorsPageItem): Result
    {
        return $this->createPastorsPageItem->create(
            pastorsPageItem: $pastorsPageItem,
        );
    }

    public function delete(string|UuidInterface $id): Result
    {
        $pastorsPageItemResult = $this->findById(id: $id);

        if (! $pastorsPageItemResult->hasPastorsPageItem) {
            return new Result();
        }

        return $this->deletePastorsPageItem->delete(
            pastorsPageItem: $pastorsPageItemResult->pastorsPageItem,
        );
    }

    public function persist(PastorsPageItem $pastorsPageItem): Result
    {
        return $this->persistPastorsPageItem->persist(
            pastorsPageItem: $pastorsPageItem,
        );
    }

    public function findAll(): PastorsPageItems
    {
        return $this->transformer->toEntities(records: $this->findAll->find());
    }

    public function findById(string|UuidInterface $id): PastorsPageItemResult
    {
        $record = $this->findById->find(
            id: $this->createUuid->fromStringOrInterface(id: $id),
        );

        if ($record === null) {
            return new PastorsPageItemResult();
        }

        $pastorsPageItem = $this->transformer->toEntity(record: $record);

        return new PastorsPageItemResult(pastorsPageItem: $pastorsPageItem);
    }
}
