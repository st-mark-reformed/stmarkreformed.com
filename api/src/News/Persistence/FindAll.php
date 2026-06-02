<?php

declare(strict_types=1);

namespace App\News\Persistence;

use App\Persistence\ApiPdo;
use PDO;
use PDOStatement;

use function assert;
use function implode;

readonly class FindAll
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(): NewsItemRecords
    {
        $statement = $this->pdo->query(implode(' ', [
            'SELECT',
            implode(', ', NewsItemRecord::getColumns()),
            'FROM news',
            'ORDER BY date DESC',
        ]));

        assert($statement instanceof PDOStatement);

        $records = $statement->fetchAll(
            PDO::FETCH_CLASS,
            NewsItemRecord::class,
        );

        /** @phpstan-ignore-next-line */
        return new NewsItemRecords(records: $records);
    }
}
