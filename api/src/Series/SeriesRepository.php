<?php

declare(strict_types=1);

namespace App\Series;

use App\Persistence\CreateUuid;
use App\Result\Result;
use App\Series\Persistence\CreateSeries;
use App\Series\Persistence\DeleteSeries;
use Ramsey\Uuid\UuidInterface;

readonly class SeriesRepository
{
    public function __construct(
        private CreateUuid $createUuid,
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
}
