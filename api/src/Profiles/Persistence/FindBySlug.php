<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

use App\Persistence\ApiPdo;
use App\Profiles\Profile\Slug;
use PDO;
use Ramsey\Uuid\UuidInterface;

use function assert;
use function implode;

readonly class FindBySlug
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(
        Slug $slug,
        UuidInterface|null $excludeId = null,
    ): ProfileRecord|null {
        $columns = implode(', ', ProfileRecord::getColumns());

        $query = [
            'SELECT',
            $columns,
            'FROM',
            ProfileRecord::getTableName(),
            'WHERE slug = :slug',
        ];

        $params = ['slug' => $slug->slug];

        if ($excludeId !== null) {
            $query[] = 'AND id != :id';

            $params['id'] = $excludeId->toString();
        }

        $statement = $this->pdo->prepare(
            implode(' ', $query),
        );

        $statement->execute($params);

        $statement->setFetchMode(
            PDO::FETCH_CLASS,
            ProfileRecord::class,
        );

        $record = $statement->fetch();

        assert($record instanceof ProfileRecord || $record === false);

        return $record !== false ? $record : null;
    }
}
