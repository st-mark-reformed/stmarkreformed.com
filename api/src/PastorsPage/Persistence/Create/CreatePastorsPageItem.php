<?php

declare(strict_types=1);

namespace App\PastorsPage\Persistence\Create;

use App\PastorsPage\NewPastorsPageItem;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class CreatePastorsPageItem
{
    public function __construct(
        private ApiPdo $pdo,
        private CreatePastorsPageItemInPdo $createPastorsPageItemInPdo,
    ) {
    }

    public function create(NewPastorsPageItem $pastorsPageItem): Result
    {
        if (! $pastorsPageItem->isValid) {
            return new Result(
                success: false,
                errors: $pastorsPageItem->validationMessages,
            );
        }

        try {
            $this->pdo->beginTransaction();

            $result = $this->createPastorsPageItemInPdo->create(
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
}
