<?php

declare(strict_types=1);

namespace App\Series\Persistence;

use App\Persistence\ApiPdo;

readonly class FindByTitle
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(string $title): SeriesRecord|null
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM series WHERE title = :title',
        );

        $statement->execute(['title' => $title]);

        $record = $statement->fetchObject(SeriesRecord::class);

        return $record instanceof SeriesRecord ? $record : null;
    }
}
