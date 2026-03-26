<?php

declare(strict_types=1);

namespace App\Series\Persistence;

use App\Persistence\ApiPdo;

readonly class FindBySlug
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(string $slug): SeriesRecord|null
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM series WHERE slug = :slug',
        );

        $statement->execute(['slug' => $slug]);

        $record = $statement->fetchObject(SeriesRecord::class);

        return $record instanceof SeriesRecord ? $record : null;
    }
}
