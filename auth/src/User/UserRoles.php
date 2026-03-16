<?php

declare(strict_types=1);

namespace App\User;

use function array_map;

readonly class UserRoles
{
    /** @param UserRole[] $roles */
    public function __construct(public array $roles = [])
    {
        array_map(
            static fn (UserRole $role) => $role,
            $roles,
        );
    }
}
