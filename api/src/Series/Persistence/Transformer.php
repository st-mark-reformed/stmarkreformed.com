<?php

declare(strict_types=1);

namespace App\Series\Persistence;

use App\Series\PopulatedSeries;
use App\Series\SeriesCollection;
use App\Series\SeriesSlug;
use Ramsey\Uuid\Uuid;

readonly class Transformer
{
    public function toEntity(SeriesRecord $record): PopulatedSeries
    {
        return new PopulatedSeries(
            id: Uuid::fromString($record->id),
            title: $record->title,
            slug: new SeriesSlug(slug: $record->slug),
        );
    }

    public function toEntities(SeriesRecords $records): SeriesCollection
    {
        return new SeriesCollection(
            items: $records->map(
                callback: fn (SeriesRecord $r) => $this->toEntity(record: $r),
            ),
        );
    }
}
