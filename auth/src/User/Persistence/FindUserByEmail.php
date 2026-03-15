<?php

declare(strict_types=1);

namespace App\User\Persistence;

use App\Persistence\AuthPdo;

readonly class FindUserByEmail
{
    public function __construct(private AuthPdo $pdo)
    {
    }

    public function find(string $email): UserRecord
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM users WHERE email = :email',
        );

        $statement->execute(['email' => $email]);

        $result = $statement->fetchObject(UserRecord::class);

        return $result instanceof UserRecord ? $result : new UserRecord();
    }
}
