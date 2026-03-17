<?php

declare(strict_types=1);

namespace App\User\Persistence;

use App\Persistence\AuthPdo;
use PDO;

use function array_map;
use function array_values;
use function count;
use function implode;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

readonly class FindUserByEmail
{
    public function __construct(private AuthPdo $pdo)
    {
    }

    /** @param string[] $excludeIds */
    public function find(
        string $email,
        array $excludeIds = [],
    ): UserRecord {
        $excludeIds = array_values($excludeIds);

        $params = ['email' => $email];

        $sql = [
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
        ];

        foreach ($excludeIds as $index => $id) {
            $pkey = 'id' . $index;

            $params[$pkey] = $id;

            $sql[] = 'AND id != :' . $pkey;
        }

        $statement = $this->pdo->prepare(
            implode(' ', $sql),
        );

        $statement->execute($params);

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
            if ($row->role === null) {
                continue;
            }

            $record->roles[] = (string) $row->role;
        }

        return $record;
    }
}
