<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

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

    public function find(UuidInterface $id): MessageRecord|null
    {
        $columns = implode(
            ', ',
            MessageRecord::getColumns(),
        );

        $statement = $this->pdo->prepare(
            implode(' ', [
                'SELECT',
                $columns,
                'FROM',
                MessageRecord::getTableName(),
                'WHERE id = :id',
            ]),
        );

        $statement->execute(['id' => $id->toString()]);

        $statement->setFetchMode(
            PDO::FETCH_CLASS,
            MessageRecord::class,
        );

        $record = $statement->fetch();

        assert($record instanceof MessageRecord || $record === false);

        return $record !== false ? $record : null;
    }
}
