<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

use App\Messages\Message\Slug;
use App\Persistence\ApiPdo;
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
    ): MessageRecord|null {
        $columns = implode(', ', MessageRecord::getColumns());

        $query = [
            'SELECT',
            $columns,
            'FROM',
            MessageRecord::getTableName(),
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
            MessageRecord::class,
        );

        $record = $statement->fetch();

        assert($record instanceof MessageRecord || $record === false);

        return $record !== false ? $record : null;
    }
}
