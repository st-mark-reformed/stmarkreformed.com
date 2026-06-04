<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Persistence;

use App\Persistence\ApiPdo;
use Ramsey\Uuid\UuidInterface;

readonly class FindById
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(UuidInterface $id): HymnOfTheMonthItemRecord|null
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM hymns_of_the_month WHERE id = :id',
        );

        $statement->execute(['id' => $id->toString()]);

        $record = $statement->fetchObject(HymnOfTheMonthItemRecord::class);

        return $record instanceof HymnOfTheMonthItemRecord ? $record : null;
    }
}
