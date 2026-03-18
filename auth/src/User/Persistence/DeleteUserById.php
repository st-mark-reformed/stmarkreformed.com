<?php

declare(strict_types=1);

namespace App\User\Persistence;

use App\Persistence\AuthPdo;
use Ramsey\Uuid\UuidInterface;
use Throwable;

readonly class DeleteUserById
{
    public function __construct(private AuthPdo $pdo)
    {
    }

    public function delete(UuidInterface $id): void
    {
        try {
            $this->pdo->beginTransaction();

            $this->pdo->prepare(
                'DELETE FROM users WHERE id = :id',
            )->execute(['id' => $id->toString()]);

            $this->pdo->prepare(
                'DELETE FROM user_roles WHERE user_id = :id',
            )->execute(['id' => $id->toString()]);

            $this->pdo->commit();
        } catch (Throwable) {
            $this->pdo->rollBack();

            return;
        }
    }
}
