<?php

declare(strict_types=1);

namespace App\Authentication\User\User;

use function array_map;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class Users
{
    /** @param User[] $users */
    public function __construct(public array $users = [])
    {
        array_map(static fn (User $u) => $u, $users);
    }

    /** @phpstan-ignore-next-line */
    public function mapToArray(callable $callback): array
    {
        return array_map($callback, $this->users);
    }
}
