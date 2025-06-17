<?php

declare(strict_types=1);

namespace App\Messages\Series\Persistence;

use App\Messages\Series\MessageSeries\MessageSeries;
use App\Messages\Series\MessageSeries\MessageSeriesCollection;
use App\Messages\Series\MessageSeries\Slug;
use App\Messages\Series\MessageSeries\Title;
use Ramsey\Uuid\Uuid;

readonly class Transformer
{
    public function createRecord(MessageSeries $fromSeries): MessageSeriesRecord
    {
        $record = new MessageSeriesRecord();

        $record->id = $fromSeries->id->toString();

        $record->title = $fromSeries->title->title;

        $record->slug = $fromSeries->slug->slug;

        return $record;
    }

    public function createMessageSeries(
        MessageSeriesRecord $fromRecord,
    ): MessageSeries {
        return new MessageSeries(
            new Title($fromRecord->title),
            new Slug($fromRecord->slug),
            Uuid::fromString($fromRecord->id),
        );
    }

    public function createMessageSeriesCollection(
        MessageSeriesRecordCollection $fromRecords,
    ): MessageSeriesCollection {
        return new MessageSeriesCollection(
            $fromRecords->mapToArray(
                fn (
                    MessageSeriesRecord $record,
                ) => $this->createMessageSeries(
                    $record,
                ),
            ),
        );
    }
}
