<?php

declare(strict_types=1);

namespace App\User;

use Stringable;

use function filter_var;

use const FILTER_VALIDATE_EMAIL;

readonly class UserEmail implements Stringable
{
    public bool $isValid;

    public function __construct(public string $email = '')
    {
        $this->isValid = filter_var(
            $email,
            FILTER_VALIDATE_EMAIL,
        ) !== false;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->email;
    }
}
