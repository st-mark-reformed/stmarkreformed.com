<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Persistence;

use App\HymnsOfTheMonth\HymnOfTheMonthItem;
use App\HymnsOfTheMonth\HymnOfTheMonthItems;
use App\HymnsOfTheMonth\HymnPracticeTracks;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;
use Throwable;

use function is_array;
use function json_decode;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class Transformer
{
    public function toEntity(HymnOfTheMonthItemRecord $record): HymnOfTheMonthItem
    {
        return new HymnOfTheMonthItem(
            id: Uuid::fromString($record->id),
            isEnabled: $record->enabled,
            date: $this->createDate($record->date),
            hymnPsalmName: $record->hymn_psalm_name,
            musicSheetPath: $record->music_sheet_path,
            practiceTracks: $this->createPracticeTracks($record->practice_tracks),
            slug: $record->slug,
        );
    }

    public function toEntities(HymnOfTheMonthItemRecords $records): HymnOfTheMonthItems
    {
        return new HymnOfTheMonthItems(
            items: $records->map(
                callback: fn (HymnOfTheMonthItemRecord $r) => $this->toEntity(
                    record: $r,
                ),
            ),
        );
    }

    private function createPracticeTracks(string $json): HymnPracticeTracks
    {
        if ($json === '') {
            return new HymnPracticeTracks();
        }

        $decoded = json_decode($json, true);

        if (! is_array($decoded)) {
            return new HymnPracticeTracks();
        }

        /** @var array<array-key, array{title?: string, path?: string}> $tracksData */
        $tracksData = $decoded;

        return HymnPracticeTracks::fromArray(raw: $tracksData);
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
