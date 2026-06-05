<?php

declare(strict_types=1);

namespace App\MailingLists\Persistence;

use App\Persistence\ApiPdo;
use PDO;
use PDOStatement;

use function assert;
use function implode;

readonly class FindSubscribers
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function all(): SubscriberRecords
    {
        $statement = $this->pdo->query(implode(' ', [
            'SELECT',
            implode(', ', SubscriberRecord::getColumns()),
            'FROM mailing_list_subscribers',
            'ORDER BY name',
        ]));

        assert($statement instanceof PDOStatement);

        $records = $statement->fetchAll(
            PDO::FETCH_CLASS,
            SubscriberRecord::class,
        );

        /** @phpstan-ignore-next-line */
        return new SubscriberRecords(records: $records);
    }

    public function forListId(string $mailingListId): SubscriberRecords
    {
        $statement = $this->pdo->prepare(implode(' ', [
            'SELECT',
            implode(', ', SubscriberRecord::getColumns()),
            'FROM mailing_list_subscribers',
            'WHERE mailing_list_id = :mailing_list_id',
            'ORDER BY name',
        ]));

        $statement->execute(['mailing_list_id' => $mailingListId]);

        $records = $statement->fetchAll(
            PDO::FETCH_CLASS,
            SubscriberRecord::class,
        );

        /** @phpstan-ignore-next-line */
        return new SubscriberRecords(records: $records);
    }
}
