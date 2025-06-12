<?php

declare(strict_types=1);

namespace App\Persistence;

use function implode;

readonly class PersistRecord
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function persist(Record $record): Result
    {
        $statement = $this->pdo->prepare(implode(' ', [
            'UPDATE',
            $record->tableName(),
            'SET',
            $record->columnsAsUpdateSetPlaceholders(),
            'WHERE id = :id',
        ]));

        if (
            ! $statement->execute($record->asUpdateParametersArray())
        ) {
            return new Result(
                false,
                $this->pdo->errorInfo(),
            );
        }

        return new Result(true, []);
    }
}
