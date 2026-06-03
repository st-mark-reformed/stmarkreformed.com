<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Persistence;

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

    public function find(): MenOfTheMarkItemRecords
    {
        $statement = $this->pdo->query(implode(' ', [
            'SELECT',
            implode(', ', MenOfTheMarkItemRecord::getColumns()),
            'FROM men_of_the_mark',
            'ORDER BY date DESC',
        ]));

        assert($statement instanceof PDOStatement);

        $records = $statement->fetchAll(
            PDO::FETCH_CLASS,
            MenOfTheMarkItemRecord::class,
        );

        /** @phpstan-ignore-next-line */
        return new MenOfTheMarkItemRecords(records: $records);
    }
}
