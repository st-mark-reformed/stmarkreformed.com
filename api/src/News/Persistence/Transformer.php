<?php

declare(strict_types=1);

namespace App\News\Persistence;

use App\News\NewsItem;
use App\News\NewsItems;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;
use Throwable;

class Transformer
{
    public function toEntity(NewsItemRecord $record): NewsItem
    {
        return new NewsItem(
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

    public function toEntities(NewsItemRecords $records): NewsItems
    {
        return new NewsItems(
            items: $records->map(
                callback: fn (NewsItemRecord $r) => $this->toEntity(record: $r),
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
