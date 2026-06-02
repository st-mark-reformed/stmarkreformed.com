<?php

declare(strict_types=1);

namespace App\News\Persistence;

use App\Persistence\ApiPdo;
use Ramsey\Uuid\UuidInterface;

readonly class FindById
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(UuidInterface $id): NewsItemRecord|null
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM news WHERE id = :id',
        );

        $statement->execute(['id' => $id->toString()]);

        $record = $statement->fetchObject(NewsItemRecord::class);

        return $record instanceof NewsItemRecord ? $record : null;
    }
}
