<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Persistence;

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

    public function find(): HymnOfTheMonthItemRecords
    {
        $statement = $this->pdo->query(implode(' ', [
            'SELECT',
            implode(', ', HymnOfTheMonthItemRecord::getColumns()),
            'FROM hymns_of_the_month',
            'ORDER BY date DESC',
        ]));

        assert($statement instanceof PDOStatement);

        $records = $statement->fetchAll(
            PDO::FETCH_CLASS,
            HymnOfTheMonthItemRecord::class,
        );

        /** @phpstan-ignore-next-line */
        return new HymnOfTheMonthItemRecords(records: $records);
    }
}
