<?php

declare(strict_types=1);

namespace App\InternalSeries\Persistence;

use App\Persistence\ApiPdo;

readonly class FindByTitle
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(string $title): InternalSeriesRecord|null
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM internal_series WHERE title = :title',
        );

        $statement->execute(['title' => $title]);

        $record = $statement->fetchObject(InternalSeriesRecord::class);

        return $record instanceof InternalSeriesRecord ? $record : null;
    }
}
