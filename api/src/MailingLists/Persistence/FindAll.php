<?php

declare(strict_types=1);

namespace App\MailingLists\Persistence;

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

    public function find(): MailingListRecords
    {
        $statement = $this->pdo->query(implode(' ', [
            'SELECT',
            implode(', ', MailingListRecord::getColumns()),
            'FROM mailing_lists',
            'ORDER BY list_name',
        ]));

        assert($statement instanceof PDOStatement);

        $records = $statement->fetchAll(
            PDO::FETCH_CLASS,
            MailingListRecord::class,
        );

        /** @phpstan-ignore-next-line */
        return new MailingListRecords(records: $records);
    }
}
