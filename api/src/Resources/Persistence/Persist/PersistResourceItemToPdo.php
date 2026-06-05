<?php

declare(strict_types=1);

namespace App\Resources\Persistence\Persist;

use App\Persistence\ApiPdo;
use App\Resources\ResourceItem;
use App\Result\Result;

use function array_keys;
use function array_map;
use function implode;
use function json_encode;

readonly class PersistResourceItemToPdo
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function persist(ResourceItem $resourceItem): Result
    {
        $params = [
            'enabled' => $resourceItem->isEnabled ? '1' : '0',
            'date' => $resourceItem->date->format('Y-m-d H:i:s'),
            'title' => $resourceItem->title,
            'slug' => $resourceItem->slug,
            'body' => $resourceItem->body,
            'downloads' => (string) json_encode(
                $resourceItem->downloads->asArray(),
            ),
        ];

        $columns = array_keys($params);

        $statement = $this->pdo->prepare(implode(' ', [
            'UPDATE resources',
            'SET',
            implode(', ', array_map(
                static fn (
                    string $column,
                ): string => $column . ' = :' . $column,
                $columns,
            )),
            'WHERE id = :id',
        ]));

        $params['id'] = $resourceItem->id->toString();

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
