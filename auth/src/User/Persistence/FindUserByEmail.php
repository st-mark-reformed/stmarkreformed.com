<?php

declare(strict_types=1);

namespace App\User\Persistence;

use App\Persistence\AuthPdo;
use PDO;

use function array_map;
use function count;
use function implode;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

readonly class FindUserByEmail
{
    public function __construct(private AuthPdo $pdo)
    {
    }

    public function find(string $email): UserRecord
    {
        $statement = $this->pdo->prepare(implode(' ', [
            'SELECT',
            implode(', ', [
                'users.id',
                'users.email',
                'users.password_hash',
                'user_roles.role',
            ]),
            'FROM users',
            'LEFT JOIN user_roles ON user_roles.user_id = users.id',
            'WHERE users.email = :email',
        ]));

        $statement->execute(['email' => $email]);

        $rows = array_map(
            /** @phpstan-ignore-next-line */
            static fn (UserRoleCompositeRecord $row) => $row,
            $statement->fetchAll(
                PDO::FETCH_CLASS,
                UserRoleCompositeRecord::class,
            ),
        );

        if (count($rows) < 1) {
            return new UserRecord();
        }

        $record = new UserRecord();

        $record->id = $rows[0]->id;

        $record->email = $rows[0]->email;

        $record->password_hash = $rows[0]->password_hash;

        foreach ($rows as $row) {
            $record->roles[] = (string) $row->role;
        }

        return $record;
    }
}
