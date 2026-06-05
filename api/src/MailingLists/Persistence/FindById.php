<?php

declare(strict_types=1);

namespace App\MailingLists\Persistence;

use App\Persistence\ApiPdo;
use Ramsey\Uuid\UuidInterface;

readonly class FindById
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(UuidInterface $id): MailingListRecord|null
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM mailing_lists WHERE id = :id',
        );

        $statement->execute(['id' => $id->toString()]);

        $record = $statement->fetchObject(MailingListRecord::class);

        return $record instanceof MailingListRecord ? $record : null;
    }
}
