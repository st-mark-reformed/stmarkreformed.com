<?php

declare(strict_types=1);

namespace App\InternalMessages\Persistence;

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

    public function find(): InternalMessagesRecords
    {
        $statement = $this->pdo->query(implode(' ', [
            'SELECT',
            implode(', ', InternalMessageRecord::getColumns()),
            'FROM internal_messages',
            'ORDER BY date DESC',
        ]));

        assert($statement instanceof PDOStatement);

        $records = $statement->fetchAll(
            PDO::FETCH_CLASS,
            InternalMessageRecord::class,
        );

        /** @phpstan-ignore-next-line */
        return new InternalMessagesRecords(records: $records);
    }
}
