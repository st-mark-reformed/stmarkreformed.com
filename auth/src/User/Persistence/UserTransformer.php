<?php

declare(strict_types=1);

namespace App\User\Persistence;

use App\EmptyUuid;
use App\User\User;
use App\User\UserEmail;
use App\User\UserRole;
use App\User\UserRoles;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Throwable;

use function array_filter;
use function array_map;
use function constant;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

readonly class UserTransformer
{
    public function fromRecord(UserRecord $record): User
    {
        $roles = array_filter(
            array_map(
                static function (string $role): UserRole|null {
                    try {
                        /** @phpstan-ignore-next-line */
                        return constant(UserRole::class . '::' . $role);
                    } catch (Throwable) {
                        return null;
                    }
                },
                $record->roles,
            ),
            static fn (UserRole|null $role): bool => $role !== null,
        );

        return new User(
            id: $this->createId($record),
            email: new UserEmail($record->email),
            passwordHash:  $record->password_hash,
            roles: new UserRoles($roles),
        );
    }

    private function createId(UserRecord $record): UuidInterface
    {
        try {
            return Uuid::fromString($record->id);
        } catch (Throwable) {
            return new EmptyUuid();
        }
    }
}
