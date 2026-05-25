<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

use App\Messages\Generate\EnqueueGenerateMessagesPagesForRedis;
use App\Persistence\ApiPdo;
use App\Result\Result;
use Ramsey\Uuid\UuidInterface;

readonly class DeleteProfile
{
    public function __construct(
        private ApiPdo $pdo,
        private EnqueueGenerateMessagesPagesForRedis $enqueueGenerateMessagesPagesForRedis,
    ) {
    }

    public function delete(UuidInterface $id): Result
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM profiles WHERE id = :id',
        );

        $success = $statement->execute(['id' => $id->toString()]);

        if ($success) {
            $this->enqueueGenerateMessagesPagesForRedis->enqueue();
        }

        return new Result(success: $success);
    }
}
