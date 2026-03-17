<?php

declare(strict_types=1);

namespace App\User;

use function array_map;
use function count;

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

    public function count(): int
    {
        return count($this->roles);
    }

    public function walk(callable $callback): void
    {
        array_map($callback, $this->roles);
    }
}
