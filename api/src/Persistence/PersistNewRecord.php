<?php

declare(strict_types=1);

namespace App\Persistence;

use function implode;

// phpcs:disable SlevomatCodingStandard.Classes.SuperfluousAbstractClassNaming.SuperfluousPrefix

readonly class PersistNewRecord
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function persist(Record $record): Result
    {
        $statement = $this->pdo->prepare(implode(' ', [
            'INSERT INTO',
            $record->tableName(),
            $record->columnsAsInsertIntoString(),
            'VALUES',
            $record->columnsAsValuePlaceholders(),
        ]));

        if (! $statement->execute($record->asParametersArray())) {
            return new Result(
                false,
                $this->pdo->errorInfo(),
            );
        }

        return new Result(true, []);
    }
}
