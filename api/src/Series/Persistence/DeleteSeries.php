<?php

declare(strict_types=1);

namespace App\Series\Persistence;

use App\Persistence\ApiPdo;
use App\Result\Result;
use Ramsey\Uuid\UuidInterface;

use function implode;

readonly class DeleteSeries
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function delete(
        UuidInterface|null $id,
        string|null $slug = null,
    ): Result {
        if ($id === null && $slug === null) {
            return new Result(
                success: false,
                errors: ['Either id or slug (or both) must be provided.'],
            );
        }

        $params = [];

        $query = ['DELETE FROM series WHERE 1 = 1'];

        if ($id !== null) {
            $query[]      = 'AND id = :id';
            $params['id'] = $id->toString();
        }

        if ($slug !== null) {
            $query[]        = 'AND slug = :slug';
            $params['slug'] = $slug;
        }

        $statement = $this->pdo->prepare(
            implode(' ', $query),
        );

        return new Result(
            success: $statement->execute($params),
        );
    }
}
