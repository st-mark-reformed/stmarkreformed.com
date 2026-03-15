<?php

declare(strict_types=1);

namespace App\User;

use function filter_var;

use const FILTER_VALIDATE_EMAIL;

readonly class UserEmail
{
    public bool $isValid;

    public function __construct(public string $email = '')
    {
        $this->isValid = filter_var(
            $this->email,
            FILTER_VALIDATE_EMAIL,
        ) !== false;
    }
}
