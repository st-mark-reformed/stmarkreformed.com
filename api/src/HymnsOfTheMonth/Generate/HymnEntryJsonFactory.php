<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Generate;

use App\HymnsOfTheMonth\HymnOfTheMonthItem;

readonly class HymnEntryJsonFactory
{
    private const string CONTENT_PREFIX =
        'Resources and tools for learning the hymn of the month: ';

    /**
     * Reproduces the payload shape the member front-end already consumes (see
     * web/app/members/hymns-of-the-month). `musicSheetFilePath` is null when no
     * sheet is present, matching the existing TypeScript contract.
     *
     * @return array{
     *     title: string,
     *     slug: string,
     *     hymnPsalmName: string,
     *     content: string,
     *     musicSheetFilePath: string|null,
     *     practiceTracks: array<array-key, array{title: string, path: string}>,
     * }
     */
    public function create(HymnOfTheMonthItem $hymnOfTheMonthItem): array
    {
        return [
            'title' => $hymnOfTheMonthItem->title,
            'slug' => $hymnOfTheMonthItem->slug,
            'hymnPsalmName' => $hymnOfTheMonthItem->hymnPsalmName,
            'content' => self::CONTENT_PREFIX . $hymnOfTheMonthItem->hymnPsalmName,
            'musicSheetFilePath' => $hymnOfTheMonthItem->musicSheetPath === ''
                ? null
                : $hymnOfTheMonthItem->musicSheetPath,
            'practiceTracks' => $hymnOfTheMonthItem->practiceTracks->asArray(),
        ];
    }
}
