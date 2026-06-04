<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth;

use App\HymnsOfTheMonth\Persistence\Create\CreateHymnOfTheMonthItem;
use App\HymnsOfTheMonth\Persistence\Delete\DeleteHymnOfTheMonthItem;
use App\HymnsOfTheMonth\Persistence\FindAll;
use App\HymnsOfTheMonth\Persistence\FindById;
use App\HymnsOfTheMonth\Persistence\Persist\PersistHymnOfTheMonthItem;
use App\HymnsOfTheMonth\Persistence\Transformer;
use App\Persistence\CreateUuid;
use App\Result\Result;
use Ramsey\Uuid\UuidInterface;

readonly class HymnsOfTheMonthRepository
{
    public function __construct(
        private FindAll $findAll,
        private FindById $findById,
        private CreateUuid $createUuid,
        private Transformer $transformer,
        private CreateHymnOfTheMonthItem $createHymnOfTheMonthItem,
        private DeleteHymnOfTheMonthItem $deleteHymnOfTheMonthItem,
        private PersistHymnOfTheMonthItem $persistHymnOfTheMonthItem,
    ) {
    }

    public function create(NewHymnOfTheMonthItem $hymnOfTheMonthItem): Result
    {
        return $this->createHymnOfTheMonthItem->create(
            hymnOfTheMonthItem: $hymnOfTheMonthItem,
        );
    }

    public function delete(string|UuidInterface $id): Result
    {
        $hymnOfTheMonthItemResult = $this->findById(id: $id);

        if (! $hymnOfTheMonthItemResult->hasHymnOfTheMonthItem) {
            return new Result();
        }

        return $this->deleteHymnOfTheMonthItem->delete(
            hymnOfTheMonthItem: $hymnOfTheMonthItemResult->hymnOfTheMonthItem,
        );
    }

    public function persist(HymnOfTheMonthItem $hymnOfTheMonthItem): Result
    {
        return $this->persistHymnOfTheMonthItem->persist(
            hymnOfTheMonthItem: $hymnOfTheMonthItem,
        );
    }

    public function findAll(): HymnOfTheMonthItems
    {
        return $this->transformer->toEntities(records: $this->findAll->find());
    }

    public function findById(string|UuidInterface $id): HymnOfTheMonthItemResult
    {
        $record = $this->findById->find(
            id: $this->createUuid->fromStringOrInterface(id: $id),
        );

        if ($record === null) {
            return new HymnOfTheMonthItemResult();
        }

        $hymnOfTheMonthItem = $this->transformer->toEntity(record: $record);

        return new HymnOfTheMonthItemResult(
            hymnOfTheMonthItem: $hymnOfTheMonthItem,
        );
    }
}
