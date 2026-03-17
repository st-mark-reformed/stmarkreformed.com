<?php

declare(strict_types=1);

namespace App\User;

readonly class CreateUserResult
{
    /** @param string[] $errors */
    public function __construct(
        public bool $success = true,
        public array $errors = [],
    ) {
    }
}
