<?php

declare(strict_types=1);

namespace App\PastorsPage\Persistence;

use App\PastorsPage\PastorsPageItem;
use App\PastorsPage\PastorsPageItems;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;
use Throwable;

class Transformer
{
    public function toEntity(PastorsPageItemRecord $record): PastorsPageItem
    {
        return new PastorsPageItem(
            id: Uuid::fromString($record->id),
            isEnabled: $record->enabled,
            date: $this->createDate($record->date),
            title: $record->title,
            slug: $record->slug,
            heading: $record->heading,
            subheading: $record->subheading,
            body: $record->body,
        );
    }

    public function toEntities(PastorsPageItemRecords $records): PastorsPageItems
    {
        return new PastorsPageItems(
            items: $records->map(
                callback: fn (PastorsPageItemRecord $r) => $this->toEntity(
                    record: $r,
                ),
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
