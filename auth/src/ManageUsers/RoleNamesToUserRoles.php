<?php

declare(strict_types=1);

namespace App\ManageUsers;

use App\User\UserRole;
use App\User\UserRoles;
use Throwable;

use function array_filter;
use function array_map;
use function constant;

readonly class RoleNamesToUserRoles
{
    /** @param string[] $roleNames */
    public function create(array $roleNames): UserRoles
    {
        $roles = array_filter(
            array_map(
                static function (string $name): UserRole|null {
                    try {
                        /** @phpstan-ignore-next-line */
                        return constant(UserRole::class . '::' . $name);
                    } catch (Throwable) {
                        return null;
                    }
                },
                $roleNames,
            ),
            static fn (UserRole|null $role): bool => $role !== null,
        );

        return new UserRoles($roles);
    }
}
