<?php

declare(strict_types=1);

namespace App\Messages\Series\Persistence;

use App\Messages\Series\MessageSeries\Slug;
use App\Persistence\ApiPdo;
use PDO;
use Ramsey\Uuid\UuidInterface;

use function assert;
use function implode;

readonly class FindBySlug
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(
        Slug $slug,
        UuidInterface|null $excludeId = null,
    ): MessageSeriesRecord|null {
        $columns = implode(
            ', ',
            MessageSeriesRecord::getColumns(),
        );

        $query = [
            'SELECT',
            $columns,
            'FROM',
            MessageSeriesRecord::getTableName(),
            'WHERE slug = :slug',
        ];

        $params = ['slug' => $slug->slug];

        if ($excludeId !== null) {
            $query[] = 'AND id != :id';

            $params['id'] = $excludeId->toString();
        }

        $statement = $this->pdo->prepare(
            implode(' ', $query),
        );

        $statement->execute($params);

        $statement->setFetchMode(
            PDO::FETCH_CLASS,
            MessageSeriesRecord::class,
        );

        $record = $statement->fetch();

        assert($record instanceof MessageSeriesRecord || $record === false);

        return $record !== false ? $record : null;
    }
}
