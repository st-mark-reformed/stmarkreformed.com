<?php

declare(strict_types=1);

namespace App\Authentication\User\Persistence;

use App\Persistence\ApiPdo;
use PDO;

use function assert;
use function implode;

readonly class FindByEmail
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(string $email): UserRecord|null
    {
        $columns = implode(', ', UserRecord::getColumns());

        $statement = $this->pdo->prepare(
            implode(' ', [
                'SELECT',
                $columns,
                'FROM',
                UserRecord::getTableName(),
                'WHERE email = :email',
            ]),
        );

        $statement->execute(['email' => $email]);

        $statement->setFetchMode(
            PDO::FETCH_CLASS,
            UserRecord::class,
        );

        $record = $statement->fetch();

        assert($record instanceof UserRecord || $record === false);

        return $record !== false ? $record : null;
    }
}
