<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Persistence\Persist;

use App\HymnsOfTheMonth\HymnOfTheMonthItem;
use App\Persistence\ApiPdo;
use App\Result\Result;

use function array_keys;
use function array_map;
use function implode;
use function json_encode;

readonly class PersistHymnOfTheMonthItemToPdo
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function persist(HymnOfTheMonthItem $hymnOfTheMonthItem): Result
    {
        $params = [
            'enabled' => $hymnOfTheMonthItem->isEnabled ? '1' : '0',
            'date' => $hymnOfTheMonthItem->date->format('Y-m-d H:i:s'),
            'slug' => $hymnOfTheMonthItem->slug,
            'hymn_psalm_name' => $hymnOfTheMonthItem->hymnPsalmName,
            'music_sheet_path' => $hymnOfTheMonthItem->musicSheetPath,
            'practice_tracks' => (string) json_encode(
                $hymnOfTheMonthItem->practiceTracks->asArray(),
            ),
        ];

        $columns = array_keys($params);

        $statement = $this->pdo->prepare(implode(' ', [
            'UPDATE hymns_of_the_month',
            'SET',
            implode(', ', array_map(
                static fn (
                    string $column,
                ): string => $column . ' = :' . $column,
                $columns,
            )),
            'WHERE id = :id',
        ]));

        $params['id'] = $hymnOfTheMonthItem->id->toString();

        $result = $statement->execute($params);

        if (! $result) {
            return new Result(
                success: false,
                errors: ['An unexpected error occurred. Please try again later.'],
            );
        }

        return new Result();
    }
}
