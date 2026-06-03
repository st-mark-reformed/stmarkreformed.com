<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Persistence;

use App\Persistence\ApiPdo;
use Ramsey\Uuid\UuidInterface;

readonly class FindById
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(UuidInterface $id): MenOfTheMarkItemRecord|null
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM men_of_the_mark WHERE id = :id',
        );

        $statement->execute(['id' => $id->toString()]);

        $record = $statement->fetchObject(MenOfTheMarkItemRecord::class);

        return $record instanceof MenOfTheMarkItemRecord ? $record : null;
    }
}
