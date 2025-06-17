<?php

declare(strict_types=1);

namespace App\Messages\Series\Persistence;

use App\Persistence\ApiPdo;
use PDO;
use Ramsey\Uuid\UuidInterface;

use function assert;
use function implode;

readonly class FindById
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(UuidInterface $id): MessageSeriesRecord|null
    {
        $columns = implode(
            ', ',
            MessageSeriesRecord::getColumns(),
        );

        $statement = $this->pdo->prepare(
            implode(' ', [
                'SELECT',
                $columns,
                'FROM',
                MessageSeriesRecord::getTableName(),
                'WHERE id = :id',
            ]),
        );

        $statement->execute(['id' => $id->toString()]);

        $statement->setFetchMode(
            PDO::FETCH_CLASS,
            MessageSeriesRecord::class,
        );

        $record = $statement->fetch();

        assert($record instanceof MessageSeriesRecord || $record === false);

        return $record !== false ? $record : null;
    }
}
