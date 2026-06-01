<?php

declare(strict_types=1);

namespace App\InternalSeries\Persistence;

use App\InternalMessages\Generate\EnqueueGenerateInternalMediaPagesForRedis;
use App\InternalSeries\PopulatedInternalSeries;
use App\Persistence\ApiPdo;
use App\Result\Result;

use function array_keys;
use function array_map;
use function implode;

readonly class PersistInternalSeries
{
    public function __construct(
        private ApiPdo $pdo,
        private FindById $findById,
        private EnqueueGenerateInternalMediaPagesForRedis $enqueueGenerateInternalMediaPagesForRedis,
    ) {
    }

    public function persist(PopulatedInternalSeries $series): Result
    {
        if (! $series->isValid) {
            return new Result(
                success: false,
                errors: $series->validationMessages,
            );
        }

        $validIdResult = $this->idIsValid(series: $series);

        if (! $validIdResult->success) {
            return $validIdResult;
        }

        $uniqueCheck = $this->isUnique(series: $series);

        if (! $uniqueCheck->success) {
            return $uniqueCheck;
        }

        $params = [
            'title' => $series->title,
            'slug' => $series->slug->toString(),
        ];

        $columns = array_keys($params);

        $statement = $this->pdo->prepare(implode(' ', [
            'UPDATE internal_series',
            'SET',
            implode(', ', array_map(
                static fn (
                    string $column,
                ): string => $column . ' = :' . $column,
                $columns,
            )),
            'WHERE id = :id',
        ]));

        $params['id'] = $series->id->toString();

        $result = $statement->execute($params);

        if (! $result) {
            return new Result(
                success: false,
                errors: ['An unexpected error occurred. Please try again later.'],
            );
        }

        $this->enqueueGenerateInternalMediaPagesForRedis->enqueue();

        return new Result();
    }

    private function idIsValid(PopulatedInternalSeries $series): Result
    {
        $record = $this->findById->find(id: $series->id);

        if ($record === null) {
            return new Result(
                success: false,
                errors: ['id' => 'Series with this ID does not exist'],
            );
        }

        return new Result();
    }

    private function isUnique(PopulatedInternalSeries $series): Result
    {
        $slugMatch = $this->findByMatchingSlug(series: $series);

        $titleMatch = $this->findByMatchingTitle(series: $series);

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

    private function findByMatchingSlug(
        PopulatedInternalSeries $series,
    ): InternalSeriesRecord|null {
        $statement = $this->pdo->prepare(
            'SELECT * FROM internal_series WHERE slug = :slug AND id != :id',
        );

        $statement->execute([
            'slug' => $series->slug->toString(),
            'id' => $series->id->toString(),
        ]);

        $record = $statement->fetchObject(InternalSeriesRecord::class);

        return $record instanceof InternalSeriesRecord ? $record : null;
    }

    private function findByMatchingTitle(
        PopulatedInternalSeries $series,
    ): InternalSeriesRecord|null {
        $statement = $this->pdo->prepare(
            'SELECT * FROM internal_series WHERE title = :title AND id != :id',
        );

        $statement->execute([
            'title' => $series->title,
            'id' => $series->id->toString(),
        ]);

        $record = $statement->fetchObject(InternalSeriesRecord::class);

        return $record instanceof InternalSeriesRecord ? $record : null;
    }
}
