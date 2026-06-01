<?php

declare(strict_types=1);

namespace App\InternalMessages\Persistence;

use App\Persistence\ApiPdo;
use Ramsey\Uuid\UuidInterface;

readonly class FindById
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(UuidInterface $id): InternalMessageRecord|null
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM internal_messages WHERE id = :id',
        );

        $statement->execute(['id' => $id->toString()]);

        $record = $statement->fetchObject(InternalMessageRecord::class);

        return $record instanceof InternalMessageRecord ? $record : null;
    }
}
