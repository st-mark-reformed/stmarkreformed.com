<?php

declare(strict_types=1);

namespace App\PastorsPage\Persistence;

use App\Persistence\ApiPdo;
use Ramsey\Uuid\UuidInterface;

readonly class FindById
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function find(UuidInterface $id): PastorsPageItemRecord|null
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM pastors_page WHERE id = :id',
        );

        $statement->execute(['id' => $id->toString()]);

        $record = $statement->fetchObject(PastorsPageItemRecord::class);

        return $record instanceof PastorsPageItemRecord ? $record : null;
    }
}
