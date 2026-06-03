<?php

declare(strict_types=1);

namespace App\PastorsPage\Persistence\Delete;

use App\PastorsPage\Generate\EnqueueGeneratePastorsPageForRedis;
use App\PastorsPage\PastorsPageItem;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class DeletePastorsPageItem
{
    public function __construct(
        private ApiPdo $pdo,
        private EnqueueGeneratePastorsPageForRedis $enqueueGeneratePastorsPageForRedis,
    ) {
    }

    public function delete(PastorsPageItem $pastorsPageItem): Result
    {
        try {
            $this->pdo->beginTransaction();

            $statement = $this->pdo->prepare(
                'DELETE FROM pastors_page WHERE id = :id',
            );

            $result = $statement->execute(
                ['id' => $pastorsPageItem->id->toString()],
            );

            if (! $result) {
                return new Result(
                    success: false,
                    errors: ['An unexpected error occurred. Please try again later.'],
                );
            }

            $this->pdo->commit();

            $this->enqueueGeneratePastorsPageForRedis->enqueue();

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
