<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Persistence\Create;

use App\MenOfTheMark\Generate\EnqueueGenerateMenOfTheMarkPagesForRedis;
use App\MenOfTheMark\NewMenOfTheMarkItem;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class CreateMenOfTheMarkItem
{
    public function __construct(
        private ApiPdo $pdo,
        private MenOfTheMarkItemInserter $menOfTheMarkItemInserter,
        private EnqueueGenerateMenOfTheMarkPagesForRedis $enqueueGenerate,
    ) {
    }

    public function create(NewMenOfTheMarkItem $menOfTheMarkItem): Result
    {
        if (! $menOfTheMarkItem->isValid) {
            return new Result(
                success: false,
                errors: $menOfTheMarkItem->validationMessages,
            );
        }

        try {
            $this->pdo->beginTransaction();

            $result = $this->menOfTheMarkItemInserter->insert(
                menOfTheMarkItem: $menOfTheMarkItem,
            );

            if (! $result->success) {
                throw $result;
            }

            $this->pdo->commit();

            $this->enqueueGenerate->enqueue();

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
