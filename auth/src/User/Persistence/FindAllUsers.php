<?php

declare(strict_types=1);

namespace App\User\Persistence;

use App\Persistence\AuthPdo;
use App\User\User;
use App\User\Users;
use PDO;

use function array_map;
use function array_values;
use function implode;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

readonly class FindAllUsers
{
    public function __construct(
        private AuthPdo $pdo,
        private UserTransformer $userTransformer,
    ) {
    }

    public function find(): Users
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
            'ORDER BY users.email ASC',
        ]));

        $statement->execute();

        $rows = array_map(
            /** @phpstan-ignore-next-line */
            static fn (UserRoleCompositeRecord $row) => $row,
            $statement->fetchAll(
                PDO::FETCH_CLASS,
                UserRoleCompositeRecord::class,
            ),
        );

        /** @var array<string, UserRecord> $recordsById */
        $recordsById = [];

        foreach ($rows as $row) {
            $record = $recordsById[$row->id] ?? null;

            if ($record === null) {
                $record = new UserRecord();

                $record->id = $row->id;

                $record->email = $row->email;

                $record->password_hash = $row->password_hash;

                $recordsById[$row->id] = $record;
            }

            if ($row->role === null) {
                continue;
            }

            $record->roles[] = (string) $row->role;
        }

        return new Users(array_map(
            fn (UserRecord $record): User => $this->userTransformer->fromRecord(
                $record,
            ),
            array_values($recordsById),
        ));
    }
}
