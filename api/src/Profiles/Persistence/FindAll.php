<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

use App\Persistence\ApiPdo;
use PDO;

use function implode;

readonly class FindAll
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(): ProfileRecords
    {
        $columns = implode(', ', ProfileRecord::getColumns());

        $statement = $this->pdo->prepare(
            implode(' ', [
                'SELECT',
                $columns,
                'FROM',
                ProfileRecord::getTableName(),
                'ORDER BY first_name, last_name ASC',
            ]),
        );

        $statement->execute();

        return new ProfileRecords($statement->fetchAll(
            PDO::FETCH_CLASS,
            ProfileRecord::class,
        ));
    }
}
