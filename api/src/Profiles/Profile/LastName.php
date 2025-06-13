<?php

declare(strict_types=1);

namespace App\Profiles\Profile;

use Assert\Assertion;
use Throwable;

readonly class LastName
{
    public bool $isValid;

    public string $errorMessage;

    public function __construct(public string $lastName)
    {
        $errorMessage = '';

        try {
            Assertion::notEmpty(
                $lastName,
                'A Last Name is required',
            );
        } catch (Throwable $e) {
            $errorMessage = $e->getMessage();
        }

        $this->isValid = $errorMessage === '';

        $this->errorMessage = $errorMessage;
    }
}
