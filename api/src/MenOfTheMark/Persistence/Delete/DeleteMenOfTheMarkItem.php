<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Persistence\Delete;

use App\MenOfTheMark\Generate\EnqueueGenerateMenOfTheMarkPagesForRedis;
use App\MenOfTheMark\MenOfTheMarkItem;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class DeleteMenOfTheMarkItem
{
    public function __construct(
        private ApiPdo $pdo,
        private EnqueueGenerateMenOfTheMarkPagesForRedis $enqueueGenerate,
    ) {
    }

    public function delete(MenOfTheMarkItem $menOfTheMarkItem): Result
    {
        try {
            $this->pdo->beginTransaction();

            $statement = $this->pdo->prepare(
                'DELETE FROM men_of_the_mark WHERE id = :id',
            );

            $result = $statement->execute(
                ['id' => $menOfTheMarkItem->id->toString()],
            );

            if (! $result) {
                return new Result(
                    success: false,
                    errors: ['An unexpected error occurred. Please try again later.'],
                );
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
