<?php

declare(strict_types=1);

namespace App\InternalSeries\Persistence;

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

    public function find(): InternalSeriesRecords
    {
        $statement = $this->pdo->query(implode(' ', [
            'SELECT',
            implode(', ', InternalSeriesRecord::getColumns()),
            'FROM internal_series',
            'ORDER BY title ASC',
        ]));

        assert($statement instanceof PDOStatement);

        $records = $statement->fetchAll(
            PDO::FETCH_CLASS,
            InternalSeriesRecord::class,
        );

        /** @phpstan-ignore-next-line */
        return new InternalSeriesRecords(records: $records);
    }
}
