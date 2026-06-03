<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Persistence\Persist;

use App\MenOfTheMark\MenOfTheMarkItem;
use App\Persistence\ApiPdo;
use App\Result\Result;

use function array_keys;
use function array_map;
use function implode;

readonly class MenOfTheMarkItemUpdater
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function update(MenOfTheMarkItem $menOfTheMarkItem): Result
    {
        $params = [
            'enabled' => $menOfTheMarkItem->isEnabled ? '1' : '0',
            'date' => $menOfTheMarkItem->date->format('Y-m-d H:i:s'),
            'title' => $menOfTheMarkItem->title,
            'slug' => $menOfTheMarkItem->slug,
            'body' => $menOfTheMarkItem->body,
        ];

        $columns = array_keys($params);

        $statement = $this->pdo->prepare(implode(' ', [
            'UPDATE men_of_the_mark',
            'SET',
            implode(', ', array_map(
                static fn (
                    string $column,
                ): string => $column . ' = :' . $column,
                $columns,
            )),
            'WHERE id = :id',
        ]));

        $params['id'] = $menOfTheMarkItem->id->toString();

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
