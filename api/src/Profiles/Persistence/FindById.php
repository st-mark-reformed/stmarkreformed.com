<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

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

    public function find(UuidInterface $id): ProfileRecord|null
    {
        $columns = implode(', ', ProfileRecord::getColumns());

        $statement = $this->pdo->prepare(
            implode(' ', [
                'SELECT',
                $columns,
                'FROM',
                ProfileRecord::getTableName(),
                'WHERE id = :id',
            ]),
        );

        $statement->execute(['id' => $id->toString()]);

        $statement->setFetchMode(
            PDO::FETCH_CLASS,
            ProfileRecord::class,
        );

        $record = $statement->fetch();

        assert($record instanceof ProfileRecord || $record === false);

        return $record !== false ? $record : null;
    }
}
