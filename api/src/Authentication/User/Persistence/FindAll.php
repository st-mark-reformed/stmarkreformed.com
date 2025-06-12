<?php

declare(strict_types=1);

namespace App\Authentication\User\Persistence;

use App\Persistence\ApiPdo;
use PDO;

use function implode;

readonly class FindAll
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(): UserRecords
    {
        $columns = implode(', ', UserRecord::getColumns());

        $statement = $this->pdo->prepare(
            implode(' ', [
                'SELECT',
                $columns,
                'FROM',
                UserRecord::getTableName(),
                'ORDER BY email ASC',
            ]),
        );

        $statement->execute();

        return new UserRecords($statement->fetchAll(
            PDO::FETCH_CLASS,
            UserRecord::class,
        ));
    }
}
