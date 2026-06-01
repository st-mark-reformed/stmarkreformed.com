<?php

declare(strict_types=1);

namespace App\InternalSeries;

use App\InternalSeries\Persistence\CreateInternalSeries;
use App\InternalSeries\Persistence\DeleteInternalSeries;
use App\InternalSeries\Persistence\FindAll;
use App\InternalSeries\Persistence\FindById;
use App\InternalSeries\Persistence\FindBySlug;
use App\InternalSeries\Persistence\FindByTitle;
use App\InternalSeries\Persistence\PersistInternalSeries;
use App\InternalSeries\Persistence\Transformer;
use App\Persistence\CreateUuid;
use App\Result\Result;
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
        private CreateInternalSeries $createSeries,
        private DeleteInternalSeries $deleteSeries,
        private PersistInternalSeries $persistSeries,
    ) {
    }

    public function create(NewInternalSeries $series): Result
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

    public function persist(PopulatedInternalSeries $series): Result
    {
        return $this->persistSeries->persist(series: $series);
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
