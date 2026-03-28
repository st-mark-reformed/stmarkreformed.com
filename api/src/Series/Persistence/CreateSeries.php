<?php

declare(strict_types=1);

namespace App\Series\Persistence;

use App\Persistence\ApiPdo;
use App\Result\Result;
use App\Series\NewSeries;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;

use function array_keys;
use function assert;
use function implode;

readonly class CreateSeries
{
    public function __construct(
        private ApiPdo $pdo,
        private FindBySlug $findBySlug,
        private FindByTitle $findByTitle,
        private UuidFactoryInterface $uuidFactory,
    ) {
    }

    public function create(NewSeries $series): Result
    {
        if (! $series->isValid) {
            return new Result(
                success: false,
                errors: $series->validationMessages,
            );
        }

        $uniqueCheck = $this->isUnique($series);

        if (! $uniqueCheck->success) {
            return $uniqueCheck;
        }

        /** @phpstan-ignore-next-line */
        $id = $this->uuidFactory->uuid7();
        assert($id instanceof UuidInterface);

        $params = [
            'id' => $id->toString(),
            'title' => $series->title,
            'slug' => $series->slug,
        ];

        $columns = array_keys($params);

        $statement = $this->pdo->prepare(implode(' ', [
            'INSERT INTO series (' . implode(', ', $columns) . ')',
            'VALUES (:' . implode(', :', $columns) . ')',
        ]));

        $result = $statement->execute($params);

        if (! $result) {
            return new Result(
                success: false,
                errors: ['An unexpected error occurred. Please try again later.'],
            );
        }

        return new Result();
    }

    private function isUnique(NewSeries $series): Result
    {
        $slugMatch = $this->findBySlug->find(slug: $series->slug->toString());

        $titleMatch = $this->findByTitle->find(title: $series->title);

        if ($slugMatch !== null && $titleMatch !== null) {
            return new Result(
                success: false,
                errors: ['Slug and title must be unique.'],
            );
        }

        if ($slugMatch !== null) {
            return new Result(
                success: false,
                errors: ['Slug must be unique.'],
            );
        }

        if ($titleMatch !== null) {
            return new Result(
                success: false,
                errors: ['Title must be unique.'],
            );
        }

        return new Result();
    }
}
