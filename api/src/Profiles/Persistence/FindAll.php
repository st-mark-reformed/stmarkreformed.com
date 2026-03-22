<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

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

    public function find(): ProfileRecords
    {
        $statement = $this->pdo->query(implode(' ', [
            'SELECT',
            implode(', ', ProfileRecord::getColumns()),
            'FROM profiles',
            'ORDER BY last_name ASC, first_name ASC, id ASC',
        ]));

        assert($statement instanceof PDOStatement);

        $records = $statement->fetchAll(
            PDO::FETCH_CLASS,
            ProfileRecord::class,
        );

        /** @phpstan-ignore-next-line */
        return new ProfileRecords(records: $records);
    }
}
