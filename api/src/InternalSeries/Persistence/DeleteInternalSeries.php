<?php

declare(strict_types=1);

namespace App\InternalSeries\Persistence;

use App\InternalMessages\Generate\EnqueueGenerateInternalMediaPagesForRedis;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Ramsey\Uuid\UuidInterface;

use function implode;

readonly class DeleteInternalSeries
{
    public function __construct(
        private ApiPdo $pdo,
        private EnqueueGenerateInternalMediaPagesForRedis $enqueueGenerateInternalMediaPagesForRedis,
    ) {
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

        $query = ['DELETE FROM internal_series WHERE 1 = 1'];

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

        $success = $statement->execute($params);

        if ($success) {
            $this->enqueueGenerateInternalMediaPagesForRedis->enqueue();
        }

        return new Result(success: $success);
    }
}
