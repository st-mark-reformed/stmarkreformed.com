<?php

declare(strict_types=1);

namespace App\Series\Persistence;

use App\Persistence\ApiPdo;
use Ramsey\Uuid\UuidInterface;

readonly class FindById
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(UuidInterface $id): SeriesRecord|null
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM series WHERE id = :id',
        );

        $statement->execute(['id' => $id->toString()]);

        $record = $statement->fetchObject(SeriesRecord::class);

        return $record instanceof SeriesRecord ? $record : null;
    }
}
