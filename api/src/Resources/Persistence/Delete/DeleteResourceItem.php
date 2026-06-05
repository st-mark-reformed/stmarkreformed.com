<?php

declare(strict_types=1);

namespace App\Resources\Persistence\Delete;

use App\Persistence\ApiPdo;
use App\Resources\Generate\EnqueueGenerateResourcesPagesForRedis;
use App\Resources\Persistence\Persist\ResourceFileStorage;
use App\Resources\ResourceItem;
use App\Result\Result;
use Throwable;

readonly class DeleteResourceItem
{
    public function __construct(
        private ApiPdo $pdo,
        private ResourceFileStorage $fileStorage,
        private EnqueueGenerateResourcesPagesForRedis $enqueueGenerateResourcesPagesForRedis,
    ) {
    }

    public function delete(ResourceItem $resourceItem): Result
    {
        try {
            $this->pdo->beginTransaction();

            $statement = $this->pdo->prepare(
                'DELETE FROM resources WHERE id = :id',
            );

            $result = $statement->execute(
                ['id' => $resourceItem->id->toString()],
            );

            if (! $result) {
                return new Result(
                    success: false,
                    errors: ['An unexpected error occurred. Please try again later.'],
                );
            }

            $this->pdo->commit();

            // Remove the resource's stored files after the row is gone.
            // Best-effort and outside the transaction: the DB is the source of
            // truth.
            $this->fileStorage->deleteAllForSlug(slug: $resourceItem->slug);

            $this->enqueueGenerateResourcesPagesForRedis->enqueue();

            return new Result();
        } catch (Throwable $error) {
            $this->pdo->rollBack();

            if ($error instanceof Result) {
                return $error;
            }

            return new Result(
                success: false,
                errors: ['An unexpected error occurred. Please try again later.'],
            );
        }
    }
}
