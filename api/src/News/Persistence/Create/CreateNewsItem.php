<?php

declare(strict_types=1);

namespace App\News\Persistence\Create;

use App\News\NewNewsItem;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class CreateNewsItem
{
    public function __construct(
        private ApiPdo $pdo,
        private CreateNewsItemInPdo $createNewsItemInPdo,
    ) {
    }

    public function create(NewNewsItem $newsItem): Result
    {
        if (! $newsItem->isValid) {
            return new Result(
                success: false,
                errors: $newsItem->validationMessages,
            );
        }

        try {
            $this->pdo->beginTransaction();

            $result = $this->createNewsItemInPdo->create(newsItem: $newsItem);

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
