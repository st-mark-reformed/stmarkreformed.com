<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Persistence\Create;

use App\EmptyUuid;
use App\HymnsOfTheMonth\NewHymnOfTheMonthItem;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;

use function array_keys;
use function assert;
use function implode;
use function json_encode;

readonly class CreateHymnOfTheMonthItemInPdo
{
    public function __construct(
        private ApiPdo $pdo,
        private UuidFactoryInterface $uuidFactory,
    ) {
    }

    public function create(NewHymnOfTheMonthItem $hymnOfTheMonthItem): Result
    {
        if ($hymnOfTheMonthItem->id instanceof EmptyUuid) {
            /** @phpstan-ignore-next-line */
            $id = $this->uuidFactory->uuid7();
            assert($id instanceof UuidInterface);
        } else {
            $id = $hymnOfTheMonthItem->id;
        }

        $params = [
            'id' => $id->toString(),
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
            'INSERT INTO hymns_of_the_month (' . implode(', ', $columns) . ')',
            'VALUES (:' . implode(', :', $columns) . ')',
        ]));

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
