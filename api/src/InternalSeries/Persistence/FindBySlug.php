<?php

declare(strict_types=1);

namespace App\InternalSeries\Persistence;

use App\Persistence\ApiPdo;

readonly class FindBySlug
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(string $slug): InternalSeriesRecord|null
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM internal_series WHERE slug = :slug',
        );

        $statement->execute(['slug' => $slug]);

        $record = $statement->fetchObject(InternalSeriesRecord::class);

        return $record instanceof InternalSeriesRecord ? $record : null;
    }
}
