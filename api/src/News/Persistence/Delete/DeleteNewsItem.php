<?php

declare(strict_types=1);

namespace App\News\Persistence\Delete;

use App\News\NewsItem;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Throwable;

readonly class DeleteNewsItem
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function delete(NewsItem $newsItem): Result
    {
        try {
            $this->pdo->beginTransaction();

            $statement = $this->pdo->prepare(
                'DELETE FROM news WHERE id = :id',
            );

            $result = $statement->execute(
                ['id' => $newsItem->id->toString()],
            );

            if (! $result) {
                return new Result(
                    success: false,
                    errors: ['An unexpected error occurred. Please try again later.'],
                );
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
