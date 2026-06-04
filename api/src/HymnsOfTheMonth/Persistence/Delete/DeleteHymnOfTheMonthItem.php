<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Persistence\Delete;

use App\HymnsOfTheMonth\Generate\EnqueueGenerateHymnsOfTheMonthForRedis;
use App\HymnsOfTheMonth\HymnOfTheMonthItem;
use App\HymnsOfTheMonth\Persistence\Persist\HymnFileStorage;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class DeleteHymnOfTheMonthItem
{
    public function __construct(
        private ApiPdo $pdo,
        private HymnFileStorage $fileStorage,
        private EnqueueGenerateHymnsOfTheMonthForRedis $enqueueGenerateHymnsOfTheMonthForRedis,
    ) {
    }

    public function delete(HymnOfTheMonthItem $hymnOfTheMonthItem): Result
    {
        try {
            $this->pdo->beginTransaction();

            $statement = $this->pdo->prepare(
                'DELETE FROM hymns_of_the_month WHERE id = :id',
            );

            $result = $statement->execute(
                ['id' => $hymnOfTheMonthItem->id->toString()],
            );

            if (! $result) {
                return new Result(
                    success: false,
                    errors: ['An unexpected error occurred. Please try again later.'],
                );
            }

            $this->pdo->commit();

            // Remove the hymn's stored files after the row is gone. Best-effort
            // and outside the transaction: the DB is the source of truth.
            $this->fileStorage->deleteAllForSlug(slug: $hymnOfTheMonthItem->slug);

            $this->enqueueGenerateHymnsOfTheMonthForRedis->enqueue();

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
