<?php

declare(strict_types=1);

namespace App\PastorsPage\Persistence\Persist;

use App\PastorsPage\PastorsPageItem;
use App\PastorsPage\Persistence\FindById;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class PersistPastorsPageItem
{
    public function __construct(
        private ApiPdo $pdo,
        private FindById $findById,
        private PersistPastorsPageItemToPdo $persistPastorsPageItemToPdo,
    ) {
    }

    public function persist(PastorsPageItem $pastorsPageItem): Result
    {
        if (! $pastorsPageItem->isValid) {
            return new Result(
                success: false,
                errors: $pastorsPageItem->validationMessages,
            );
        }

        $validIdResult = $this->idIsValid(pastorsPageItem: $pastorsPageItem);

        if (! $validIdResult->success) {
            return $validIdResult;
        }

        try {
            $this->pdo->beginTransaction();

            $result = $this->persistPastorsPageItemToPdo->persist(
                pastorsPageItem: $pastorsPageItem,
            );

            if (! $result->success) {
                throw $result;
            }

            $this->pdo->commit();

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

    private function idIsValid(PastorsPageItem $pastorsPageItem): Result
    {
        $record = $this->findById->find(id: $pastorsPageItem->id);

        if ($record === null) {
            return new Result(
                success: false,
                errors: ['id' => 'Pastors page item with this ID does not exist'],
            );
        }

        return new Result();
    }
}
