<?php

declare(strict_types=1);

namespace App\Series\Persistence;

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

    public function find(): SeriesRecords
    {
        $statement = $this->pdo->query(implode(' ', [
            'SELECT',
            implode(', ', SeriesRecord::getColumns()),
            'FROM series',
            'ORDER BY title ASC',
        ]));

        assert($statement instanceof PDOStatement);

        $records = $statement->fetchAll(
            PDO::FETCH_CLASS,
            SeriesRecord::class,
        );

        /** @phpstan-ignore-next-line */
        return new SeriesRecords(records: $records);
    }
}
