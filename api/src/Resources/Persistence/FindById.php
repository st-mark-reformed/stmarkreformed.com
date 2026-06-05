<?php

declare(strict_types=1);

namespace App\Resources\Persistence;

use App\Persistence\ApiPdo;
use Ramsey\Uuid\UuidInterface;

readonly class FindById
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(UuidInterface $id): ResourceItemRecord|null
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM resources WHERE id = :id',
        );

        $statement->execute(['id' => $id->toString()]);

        $record = $statement->fetchObject(ResourceItemRecord::class);

        return $record instanceof ResourceItemRecord ? $record : null;
    }
}
