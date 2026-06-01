<?php

declare(strict_types=1);

namespace App\InternalSeries\Persistence;

use App\Persistence\ApiPdo;
use Ramsey\Uuid\UuidInterface;

readonly class FindById
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(UuidInterface $id): InternalSeriesRecord|null
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM internal_series WHERE id = :id',
        );

        $statement->execute(['id' => $id->toString()]);

        $record = $statement->fetchObject(InternalSeriesRecord::class);

        return $record instanceof InternalSeriesRecord ? $record : null;
    }
}
