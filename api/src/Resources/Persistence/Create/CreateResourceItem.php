<?php

declare(strict_types=1);

namespace App\Resources\Persistence\Create;

use App\Persistence\ApiPdo;
use App\Resources\Generate\EnqueueGenerateResourcesPagesForRedis;
use App\Resources\NewResourceItem;
use App\Result\Result;
use Throwable;

readonly class CreateResourceItem
{
    public function __construct(
        private ApiPdo $pdo,
        private CreateResourceItemInPdo $createResourceItemInPdo,
        private EnqueueGenerateResourcesPagesForRedis $enqueueGenerateResourcesPagesForRedis,
    ) {
    }

    public function create(NewResourceItem $resourceItem): Result
    {
        if (! $resourceItem->isValid) {
            return new Result(
                success: false,
                errors: $resourceItem->validationMessages,
            );
        }

        try {
            $this->pdo->beginTransaction();

            $result = $this->createResourceItemInPdo->create(
                resourceItem: $resourceItem,
            );

            if (! $result->success) {
                throw $result;
            }

            $this->pdo->commit();

            $this->enqueueGenerateResourcesPagesForRedis->enqueue();

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
