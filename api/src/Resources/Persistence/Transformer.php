<?php

declare(strict_types=1);

namespace App\Resources\Persistence;

use App\Resources\ResourceDownloads;
use App\Resources\ResourceItem;
use App\Resources\ResourceItems;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;
use Throwable;

use function is_array;
use function json_decode;

class Transformer
{
    public function toEntity(ResourceItemRecord $record): ResourceItem
    {
        return new ResourceItem(
            id: Uuid::fromString($record->id),
            isEnabled: $record->enabled,
            date: $this->createDate($record->date),
            title: $record->title,
            slug: $record->slug,
            body: $record->body,
            downloads: $this->createDownloads($record->downloads),
        );
    }

    public function toEntities(ResourceItemRecords $records): ResourceItems
    {
        return new ResourceItems(
            items: $records->map(
                callback: fn (ResourceItemRecord $r) => $this->toEntity(record: $r),
            ),
        );
    }

    private function createDownloads(string $json): ResourceDownloads
    {
        if ($json === '') {
            return new ResourceDownloads();
        }

        $decoded = json_decode($json, true);

        if (! is_array($decoded)) {
            return new ResourceDownloads();
        }

        /** @var array<array-key, array{filename?: string}> $downloadsData */
        $downloadsData = $decoded;

        return ResourceDownloads::fromArray(raw: $downloadsData);
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
