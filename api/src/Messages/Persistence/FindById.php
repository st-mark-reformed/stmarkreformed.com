<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

use App\Persistence\ApiPdo;
use Ramsey\Uuid\UuidInterface;

readonly class FindById
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(UuidInterface $id): MessageRecord|null
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM messages WHERE id = :id',
        );

        $statement->execute(['id' => $id->toString()]);

        $record = $statement->fetchObject(MessageRecord::class);

        return $record instanceof MessageRecord ? $record : null;
    }
}
