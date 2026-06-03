<?php

declare(strict_types=1);

namespace App\MenOfTheMark;

use App\MenOfTheMark\Persistence\Create\CreateMenOfTheMarkItem;
use App\MenOfTheMark\Persistence\Delete\DeleteMenOfTheMarkItem;
use App\MenOfTheMark\Persistence\FindAll;
use App\MenOfTheMark\Persistence\FindById;
use App\MenOfTheMark\Persistence\Persist\PersistMenOfTheMarkItem;
use App\MenOfTheMark\Persistence\Transformer;
use App\Persistence\CreateUuid;
use App\Result\Result;
use Ramsey\Uuid\UuidInterface;

readonly class MenOfTheMarkRepository
{
    public function __construct(
        private FindAll $findAll,
        private FindById $findById,
        private CreateUuid $createUuid,
        private Transformer $transformer,
        private CreateMenOfTheMarkItem $createMenOfTheMarkItem,
        private DeleteMenOfTheMarkItem $deleteMenOfTheMarkItem,
        private PersistMenOfTheMarkItem $persistMenOfTheMarkItem,
    ) {
    }

    public function create(NewMenOfTheMarkItem $menOfTheMarkItem): Result
    {
        return $this->createMenOfTheMarkItem->create(
            menOfTheMarkItem: $menOfTheMarkItem,
        );
    }

    public function delete(string|UuidInterface $id): Result
    {
        $result = $this->findById(id: $id);

        if (! $result->hasMenOfTheMarkItem) {
            return new Result();
        }

        return $this->deleteMenOfTheMarkItem->delete(
            menOfTheMarkItem: $result->menOfTheMarkItem,
        );
    }

    public function persist(MenOfTheMarkItem $menOfTheMarkItem): Result
    {
        return $this->persistMenOfTheMarkItem->persist(
            menOfTheMarkItem: $menOfTheMarkItem,
        );
    }

    public function findAll(): MenOfTheMarkItems
    {
        return $this->transformer->toEntities(records: $this->findAll->find());
    }

    public function findById(string|UuidInterface $id): MenOfTheMarkItemResult
    {
        $record = $this->findById->find(
            id: $this->createUuid->fromStringOrInterface(id: $id),
        );

        if ($record === null) {
            return new MenOfTheMarkItemResult();
        }

        $menOfTheMarkItem = $this->transformer->toEntity(record: $record);

        return new MenOfTheMarkItemResult(menOfTheMarkItem: $menOfTheMarkItem);
    }
}
