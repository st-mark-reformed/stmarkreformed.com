<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

use App\Persistence\ApiPdo;
use PDO;

use function implode;

readonly class FindAll
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(): MessageRecords
    {
        $columns = implode(', ', MessageRecord::getColumns());

        $statement = $this->pdo->prepare(
            implode(' ', [
                'SELECT',
                $columns,
                'FROM',
                MessageRecord::getTableName(),
                'ORDER BY date DESC',
            ]),
        );

        $statement->execute();

        return new MessageRecords($statement->fetchAll(
            PDO::FETCH_CLASS,
            MessageRecord::class,
        ));
    }
}
