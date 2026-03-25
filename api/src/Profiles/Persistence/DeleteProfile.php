<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

use App\Persistence\ApiPdo;
use App\Result\Result;
use Ramsey\Uuid\UuidInterface;

readonly class DeleteProfile
{
    public function __construct(private ApiPdo $pdo)
    {
    }

    public function delete(UuidInterface $id): Result
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM profiles WHERE id = :id',
        );

        return new Result(
            success: $statement->execute(['id' => $id->toString()]),
        );
    }
}
