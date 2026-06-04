<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Persistence\Persist;

use App\HymnsOfTheMonth\Generate\EnqueueGenerateHymnsOfTheMonthForRedis;
use App\HymnsOfTheMonth\HymnOfTheMonthItem;
use App\HymnsOfTheMonth\Persistence\FindById;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class PersistHymnOfTheMonthItem
{
    public function __construct(
        private ApiPdo $pdo,
        private FindById $findById,
        private PersistHymnOfTheMonthItemToPdo $persistHymnOfTheMonthItemToPdo,
        private EnqueueGenerateHymnsOfTheMonthForRedis $enqueueGenerateHymnsOfTheMonthForRedis,
    ) {
    }

    public function persist(HymnOfTheMonthItem $hymnOfTheMonthItem): Result
    {
        if (! $hymnOfTheMonthItem->isValid) {
            return new Result(
                success: false,
                errors: $hymnOfTheMonthItem->validationMessages,
            );
        }

        $validIdResult = $this->idIsValid(
            hymnOfTheMonthItem: $hymnOfTheMonthItem,
        );

        if (! $validIdResult->success) {
            return $validIdResult;
        }

        try {
            $this->pdo->beginTransaction();

            $result = $this->persistHymnOfTheMonthItemToPdo->persist(
                hymnOfTheMonthItem: $hymnOfTheMonthItem,
            );

            if (! $result->success) {
                throw $result;
            }

            $this->pdo->commit();

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

    private function idIsValid(HymnOfTheMonthItem $hymnOfTheMonthItem): Result
    {
        $record = $this->findById->find(id: $hymnOfTheMonthItem->id);

        if ($record === null) {
            return new Result(
                success: false,
                errors: ['id' => 'Hymn of the month item with this ID does not exist'],
            );
        }

        return new Result();
    }
}
