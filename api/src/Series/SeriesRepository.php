<?php

declare(strict_types=1);

namespace App\Series;

use App\Persistence\CreateUuid;
use App\Result\Result;
use App\Series\Persistence\CreateSeries;
use App\Series\Persistence\DeleteSeries;
use App\Series\Persistence\FindAll;
use App\Series\Persistence\FindById;
use App\Series\Persistence\Transformer;
use Ramsey\Uuid\UuidInterface;

readonly class SeriesRepository
{
    public function __construct(
        private FindAll $findAll,
        private FindById $findById,
        private CreateUuid $createUuid,
        private Transformer $transformer,
        private CreateSeries $createSeries,
        private DeleteSeries $deleteSeries,
    ) {
    }

    public function create(NewSeries $series): Result
    {
        return $this->createSeries->create(series: $series);
    }

    public function delete(
        string|UuidInterface|null $id = null,
        string|null $slug = null,
    ): Result {
        if ($id !== null) {
            $id = $this->createUuid->fromStringOrInterface(id: $id);
        }

        return $this->deleteSeries->delete(
            id: $id,
            slug: $slug,
        );
    }

    public function findAll(): SeriesCollection
    {
        return $this->transformer->toEntities(records: $this->findAll->find());
    }

    public function findById(string|UuidInterface $id): SeriesResult
    {
        $record = $this->findById->find(
            id: $this->createUuid->fromStringOrInterface(id: $id),
        );

        if ($record === null) {
            return new SeriesResult();
        }

        $series = $this->transformer->toEntity(record: $record);

        return new SeriesResult(series: $series);
    }
}
