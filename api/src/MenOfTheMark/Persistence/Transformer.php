<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Persistence;

use App\MenOfTheMark\MenOfTheMarkItem;
use App\MenOfTheMark\MenOfTheMarkItems;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;
use Throwable;

class Transformer
{
    public function toEntity(MenOfTheMarkItemRecord $record): MenOfTheMarkItem
    {
        return new MenOfTheMarkItem(
            id: Uuid::fromString($record->id),
            isEnabled: $record->enabled,
            date: $this->createDate($record->date),
            title: $record->title,
            slug: $record->slug,
            body: $record->body,
        );
    }

    public function toEntities(MenOfTheMarkItemRecords $records): MenOfTheMarkItems
    {
        return new MenOfTheMarkItems(
            items: $records->map(
                callback: fn (
                    MenOfTheMarkItemRecord $r,
                ) => $this->toEntity(record: $r),
            ),
        );
    }

    private function createDate(string $date): DateTimeImmutable
    {
        try {
            /** @phpstan-ignore-next-line */
            return DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                $date,
                new DateTimeZone('US/Central'),
            );
        } catch (Throwable) {
            /** @phpstan-ignore-next-line */
            return DateTimeImmutable::createFromFormat(
                'Y-m-d',
                '1900-01-01',
                new DateTimeZone('US/Central'),
            );
        }
    }
}
