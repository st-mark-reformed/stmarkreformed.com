<?php

declare(strict_types=1);

namespace App\PastorsPage\Persistence\Persist;

use App\PastorsPage\PastorsPageItem;
use App\Persistence\ApiPdo;
use App\Result\Result;

use function array_keys;
use function array_map;
use function implode;

readonly class PersistPastorsPageItemToPdo
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function persist(PastorsPageItem $pastorsPageItem): Result
    {
        $params = [
            'enabled' => $pastorsPageItem->isEnabled ? '1' : '0',
            'date' => $pastorsPageItem->date->format('Y-m-d H:i:s'),
            'title' => $pastorsPageItem->title,
            'slug' => $pastorsPageItem->slug,
            'heading' => $pastorsPageItem->heading,
            'subheading' => $pastorsPageItem->subheading,
            'body' => $pastorsPageItem->body,
        ];

        $columns = array_keys($params);

        $statement = $this->pdo->prepare(implode(' ', [
            'UPDATE pastors_page',
            'SET',
            implode(', ', array_map(
                static fn (
                    string $column,
                ): string => $column . ' = :' . $column,
                $columns,
            )),
            'WHERE id = :id',
        ]));

        $params['id'] = $pastorsPageItem->id->toString();

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
