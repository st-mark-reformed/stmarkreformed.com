<?php

declare(strict_types=1);

namespace App\Resources\Persistence;

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

    public function find(): ResourceItemRecords
    {
        $statement = $this->pdo->query(implode(' ', [
            'SELECT',
            implode(', ', ResourceItemRecord::getColumns()),
            'FROM resources',
            'ORDER BY date DESC',
        ]));

        assert($statement instanceof PDOStatement);

        $records = $statement->fetchAll(
            PDO::FETCH_CLASS,
            ResourceItemRecord::class,
        );

        /** @phpstan-ignore-next-line */
        return new ResourceItemRecords(records: $records);
    }
}
