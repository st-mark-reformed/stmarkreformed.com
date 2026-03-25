<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

use App\Persistence\ApiPdo;
use Ramsey\Uuid\UuidInterface;

readonly class FindById
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(UuidInterface $id): ProfileRecord|null
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM profiles WHERE id = :id',
        );

        $statement->execute(['id' => $id->toString()]);

        $record = $statement->fetchObject(ProfileRecord::class);

        return $record instanceof ProfileRecord ? $record : null;
    }
}
