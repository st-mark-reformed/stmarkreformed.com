<?php

declare(strict_types=1);

namespace App\Resources\Persistence\Persist;

use App\Persistence\ApiPdo;
use App\Resources\Persistence\FindById;
use App\Resources\ResourceItem;
use App\Result\Result;
use Throwable;

readonly class PersistResourceItem
{
    public function __construct(
        private ApiPdo $pdo,
        private FindById $findById,
        private PersistResourceItemToPdo $persistResourceItemToPdo,
    ) {
    }

    public function persist(ResourceItem $resourceItem): Result
    {
        if (! $resourceItem->isValid) {
            return new Result(
                success: false,
                errors: $resourceItem->validationMessages,
            );
        }

        $validIdResult = $this->idIsValid(resourceItem: $resourceItem);

        if (! $validIdResult->success) {
            return $validIdResult;
        }

        try {
            $this->pdo->beginTransaction();

            $result = $this->persistResourceItemToPdo->persist(
                resourceItem: $resourceItem,
            );

            if (! $result->success) {
                throw $result;
            }

            $this->pdo->commit();

            // Redis generation is enqueued here once the generation slice lands.

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

    private function idIsValid(ResourceItem $resourceItem): Result
    {
        $record = $this->findById->find(id: $resourceItem->id);

        if ($record === null) {
            return new Result(
                success: false,
                errors: ['id' => 'Resource with this ID does not exist'],
            );
        }

        return new Result();
    }
}
