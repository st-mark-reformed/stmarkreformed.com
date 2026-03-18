<?php

declare(strict_types=1);

namespace App\User\Persistence;

use App\Persistence\AuthPdo;
use App\User\UserRole;
use Exception;
use Ramsey\Uuid\UuidInterface;

use function implode;

readonly class InsertUserRole
{
    public function __construct(private AuthPdo $pdo)
    {
    }

    public function insert(UuidInterface $id, UserRole $role): void
    {
        $statement = $this->pdo->prepare(implode(' ', [
            'INSERT INTO user_roles (user_id, role)',
            'VALUES (:user_id, :role)',
        ]));

        $result = $statement->execute([
            'user_id' => $id->toString(),
            'role' => $role->name,
        ]);

        if ($result) {
            return;
        }

        throw new Exception('Failed to insert role');
    }
}
