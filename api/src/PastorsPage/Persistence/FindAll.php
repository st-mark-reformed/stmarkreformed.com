<?php

declare(strict_types=1);

namespace App\PastorsPage\Persistence;

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

    public function find(): PastorsPageItemRecords
    {
        $statement = $this->pdo->query(implode(' ', [
            'SELECT',
            implode(', ', PastorsPageItemRecord::getColumns()),
            'FROM pastors_page',
            'ORDER BY date DESC',
        ]));

        assert($statement instanceof PDOStatement);

        $records = $statement->fetchAll(
            PDO::FETCH_CLASS,
            PastorsPageItemRecord::class,
        );

        /** @phpstan-ignore-next-line */
        return new PastorsPageItemRecords(records: $records);
    }
}
