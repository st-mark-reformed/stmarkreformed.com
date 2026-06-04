<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Persistence\Create;

use App\HymnsOfTheMonth\Generate\EnqueueGenerateHymnsOfTheMonthForRedis;
use App\HymnsOfTheMonth\NewHymnOfTheMonthItem;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class CreateHymnOfTheMonthItem
{
    public function __construct(
        private ApiPdo $pdo,
        private CreateHymnOfTheMonthItemInPdo $createHymnOfTheMonthItemInPdo,
        private EnqueueGenerateHymnsOfTheMonthForRedis $enqueueGenerateHymnsOfTheMonthForRedis,
    ) {
    }

    public function create(NewHymnOfTheMonthItem $hymnOfTheMonthItem): Result
    {
        if (! $hymnOfTheMonthItem->isValid) {
            return new Result(
                success: false,
                errors: $hymnOfTheMonthItem->validationMessages,
            );
        }

        try {
            $this->pdo->beginTransaction();

            $result = $this->createHymnOfTheMonthItemInPdo->create(
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
}
