<?php

declare(strict_types=1);

namespace App\Authentication\User\User;

use function array_map;
use function json_encode;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class Roles
{
    public bool $isValid;

    public string $errorMessage;

    /** @param Role[] $roles */
    public function __construct(public array $roles = [])
    {
        $isValid = true;

        $errorMessage = '';

        array_map(static function (Role $role) use (
            &$isValid,
            &$errorMessage,
        ): void {
            if ($role !== Role::INVALID) {
                return;
            }

            $isValid = false;

            $errorMessage = 'Role must be valid';
        }, $roles);

        $this->isValid = $isValid;

        $this->errorMessage = $errorMessage;
    }

    public function asString(): string
    {
        return (string) json_encode(array_map(
            static fn (Role $r) => $r->name,
            $this->roles,
        ));
    }

    /** @phpstan-ignore-next-line */
    public function mapToArray(callable $callback): array
    {
        return array_map($callback, $this->roles);
    }
}
