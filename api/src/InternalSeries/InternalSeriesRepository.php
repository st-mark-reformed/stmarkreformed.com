<?php

declare(strict_types=1);

namespace App\InternalSeries;

use App\InternalSeries\Persistence\FindAll;
use App\InternalSeries\Persistence\FindById;
use App\InternalSeries\Persistence\FindBySlug;
use App\InternalSeries\Persistence\FindByTitle;
use App\InternalSeries\Persistence\Transformer;
use App\Persistence\CreateUuid;
use Ramsey\Uuid\UuidInterface;

readonly class InternalSeriesRepository
{
    public function __construct(
        private FindAll $findAll,
        private FindById $findById,
        private CreateUuid $createUuid,
        private FindBySlug $findBySlug,
        private Transformer $transformer,
        private FindByTitle $findByTitle,
    ) {
    }

    public function findAll(): InternalSeriesCollection
    {
        return $this->transformer->toEntities(records: $this->findAll->find());
    }

    public function findById(string|UuidInterface $id): InternalSeriesResult
    {
        $record = $this->findById->find(
            id: $this->createUuid->fromStringOrInterface(id: $id),
        );

        if ($record === null) {
            return new InternalSeriesResult();
        }

        $series = $this->transformer->toEntity(record: $record);

        return new InternalSeriesResult(series: $series);
    }

    public function findBySlug(string $slug): InternalSeriesResult
    {
        $record = $this->findBySlug->find(slug: $slug);

        if ($record === null) {
            return new InternalSeriesResult();
        }

        $series = $this->transformer->toEntity(record: $record);

        return new InternalSeriesResult(series: $series);
    }

    public function findByTitle(string $title): InternalSeriesResult
    {
        $record = $this->findByTitle->find(title: $title);

        if ($record === null) {
            return new InternalSeriesResult();
        }

        $series = $this->transformer->toEntity(record: $record);

        return new InternalSeriesResult(series: $series);
    }
}
