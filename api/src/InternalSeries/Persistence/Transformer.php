<?php

declare(strict_types=1);

namespace App\InternalSeries\Persistence;

use App\InternalSeries\InternalSeriesCollection;
use App\InternalSeries\InternalSeriesSlug;
use App\InternalSeries\PopulatedInternalSeries;
use Ramsey\Uuid\Uuid;

readonly class Transformer
{
    public function toEntity(InternalSeriesRecord $record): PopulatedInternalSeries
    {
        return new PopulatedInternalSeries(
            id: Uuid::fromString($record->id),
            title: $record->title,
            slug: new InternalSeriesSlug(slug: $record->slug),
        );
    }

    public function toEntities(
        InternalSeriesRecords $records,
    ): InternalSeriesCollection {
        return new InternalSeriesCollection(
            items: $records->map(
                callback: fn (InternalSeriesRecord $r) => $this->toEntity(
                    record: $r,
                ),
            ),
        );
    }
}
