<?php

declare(strict_types=1);

namespace App\Profiles;

use Stringable;

use function filter_var;

use const FILTER_VALIDATE_EMAIL;

readonly class ProfileEmail implements Stringable
{
    public bool $isValid;

    public function __construct(public string $email = '')
    {
        $this->isValid = $email === '' || filter_var(
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
