<?php

declare(strict_types=1);

namespace App\User;

use function array_filter;
use function array_find;
use function array_map;
use function array_values;
use function count;

readonly class UserRoles
{
    /** @var UserRole[] */
    public array $roles;

    /** @param UserRole[] $roles */
    public function __construct(array $roles = [])
    {
        $alreadyUsed = [];

        $this->roles = array_filter(
            array_values(array_map(
                static fn (UserRole $role) => $role,
                $roles,
            )),
            static function (
                UserRole $role,
            ) use (&$alreadyUsed): bool {
                $used = array_find(
                    $alreadyUsed,
                    static function (UserRole $usedRole) use ($role): bool {
                        return $usedRole->name === $role->name;
                    },
                );

                /** @phpstan-ignore-next-line */
                $alreadyUsed[] = $role;

                return $used === null;
            },
        );
    }

    public function count(): int
    {
        return count($this->roles);
    }

    /** @return string[] */
    public function asArray(): array
    {
        return array_map(
            static fn (UserRole $role): string => $role->name,
            $this->roles,
        );
    }

    public function find(callable $callback): UserRole|null
    {
        return array_find($this->roles, $callback);
    }

    public function filter(callable $callback): self
    {
        return new self(array_filter(
            $this->roles,
            $callback,
        ));
    }

    public function walk(callable $callback): void
    {
        array_map($callback, $this->roles);
    }

    public function withAddedRole(UserRole $role): self
    {
        return new self(roles: [...$this->roles, $role]);
    }

    /** @param UserRole[] $roles */
    public function withAddedRoles(array $roles): self
    {
        return new self(roles: [...$this->roles, ...$roles]);
    }

    public function withRemovedRole(UserRole $role): self
    {
        return new self(roles: array_filter(
            $this->roles,
            static function (UserRole $existing) use ($role): bool {
                return $existing->name !== $role->name;
            },
        ));
    }

    /** @param UserRole[] $roles */
    public function withRemovedRoles(array $roles): self
    {
        $newRoles = array_filter(
            $this->roles,
            static function (UserRole $existingRole) use ($roles): bool {
                $isKeeper = array_find(
                    $roles,
                    static fn (
                        UserRole $keepRole,
                    ) => $keepRole->name === $existingRole->name,
                );

                return $isKeeper === null;
            },
        );

        return new self(roles: $newRoles);
    }
}
