<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Persistence\Persist;

use App\MenOfTheMark\MenOfTheMarkItem;
use App\MenOfTheMark\Persistence\FindById;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class PersistMenOfTheMarkItem
{
    public function __construct(
        private ApiPdo $pdo,
        private FindById $findById,
        private MenOfTheMarkItemUpdater $menOfTheMarkItemUpdater,
    ) {
    }

    public function persist(MenOfTheMarkItem $menOfTheMarkItem): Result
    {
        if (! $menOfTheMarkItem->isValid) {
            return new Result(
                success: false,
                errors: $menOfTheMarkItem->validationMessages,
            );
        }

        $validIdResult = $this->idIsValid(menOfTheMarkItem: $menOfTheMarkItem);

        if (! $validIdResult->success) {
            return $validIdResult;
        }

        try {
            $this->pdo->beginTransaction();

            $result = $this->menOfTheMarkItemUpdater->update(
                menOfTheMarkItem: $menOfTheMarkItem,
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

    private function idIsValid(MenOfTheMarkItem $menOfTheMarkItem): Result
    {
        $record = $this->findById->find(id: $menOfTheMarkItem->id);

        if ($record === null) {
            return new Result(
                success: false,
                errors: ['id' => 'Men of the Mark item with this ID does not exist'],
            );
        }

        return new Result();
    }
}
