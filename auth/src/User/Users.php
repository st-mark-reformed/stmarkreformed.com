<?php

declare(strict_types=1);

namespace App\User;

use function array_map;
use function array_values;
use function count;

readonly class Users
{
    /** @var User[] */
    public array $users;

    /** @param User[] $users */
    public function __construct(array $users = [])
    {
        $this->users = array_values(array_map(
            static fn (User $user): User => $user,
            $users,
        ));
    }

    public function count(): int
    {
        return count($this->users);
    }

    public function hasUsers(): bool
    {
        return $this->count() > 0;
    }

    public function walk(callable $callback): void
    {
        array_map($callback, $this->users);
    }
}
