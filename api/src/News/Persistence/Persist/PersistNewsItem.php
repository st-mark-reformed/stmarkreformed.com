<?php

declare(strict_types=1);

namespace App\News\Persistence\Persist;

use App\News\NewsItem;
use App\News\Persistence\FindById;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class PersistNewsItem
{
    public function __construct(
        private ApiPdo $pdo,
        private FindById $findById,
        private PersistNewsItemToPdo $persistNewsItemToPdo,
    ) {
    }

    public function persist(NewsItem $newsItem): Result
    {
        if (! $newsItem->isValid) {
            return new Result(
                success: false,
                errors: $newsItem->validationMessages,
            );
        }

        $validIdResult = $this->idIsValid(newsItem: $newsItem);

        if (! $validIdResult->success) {
            return $validIdResult;
        }

        try {
            $this->pdo->beginTransaction();

            $result = $this->persistNewsItemToPdo->persist(newsItem: $newsItem);

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

    private function idIsValid(NewsItem $newsItem): Result
    {
        $record = $this->findById->find(id: $newsItem->id);

        if ($record === null) {
            return new Result(
                success: false,
                errors: ['id' => 'News item with this ID does not exist'],
            );
        }

        return new Result();
    }
}
