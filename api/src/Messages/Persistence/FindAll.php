<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

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

    public function find(): MessagesRecords
    {
        $statement = $this->pdo->query(implode(' ', [
            'SELECT',
            implode(', ', MessageRecord::getColumns()),
            'FROM messages',
            'ORDER BY date DESC',
        ]));

        assert($statement instanceof PDOStatement);

        $records = $statement->fetchAll(
            PDO::FETCH_CLASS,
            MessageRecord::class,
        );

        /** @phpstan-ignore-next-line */
        return new MessagesRecords(records: $records);
    }
}
