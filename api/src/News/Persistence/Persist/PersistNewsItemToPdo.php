<?php

declare(strict_types=1);

namespace App\News\Persistence\Persist;

use App\News\NewsItem;
use App\Persistence\ApiPdo;
use App\Result\Result;

use function array_keys;
use function array_map;
use function implode;

readonly class PersistNewsItemToPdo
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function persist(NewsItem $newsItem): Result
    {
        $params = [
            'enabled' => $newsItem->isEnabled ? '1' : '0',
            'date' => $newsItem->date->format('Y-m-d H:i:s'),
            'title' => $newsItem->title,
            'slug' => $newsItem->slug,
            'heading' => $newsItem->heading,
            'subheading' => $newsItem->subheading,
            'body' => $newsItem->body,
        ];

        $columns = array_keys($params);

        $statement = $this->pdo->prepare(implode(' ', [
            'UPDATE news',
            'SET',
            implode(', ', array_map(
                static fn (
                    string $column,
                ): string => $column . ' = :' . $column,
                $columns,
            )),
            'WHERE id = :id',
        ]));

        $params['id'] = $newsItem->id->toString();

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
