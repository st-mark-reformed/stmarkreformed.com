<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

use App\Persistence\ApiPdo;
use PDO;

use function assert;
use function implode;

readonly class FindByFullNameWithHonorific
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(
        string $honorific,
        string $firstName,
        string $lastName,
    ): ProfileRecord|null {
        $columns = implode(', ', ProfileRecord::getColumns());

        $statement = $this->pdo->prepare(
            implode(' ', [
                'SELECT',
                $columns,
                'FROM',
                ProfileRecord::getTableName(),
                'WHERE title_or_honorific = :title_or_honorific',
                'AND first_name = :first_name',
                'AND last_name = :last_name',
            ]),
        );

        $statement->execute([
            'title_or_honorific' => $honorific,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ]);

        $statement->setFetchMode(
            PDO::FETCH_CLASS,
            ProfileRecord::class,
        );

        $record = $statement->fetch();

        assert($record instanceof ProfileRecord || $record === false);

        return $record !== false ? $record : null;
    }
}
