<?php

declare(strict_types=1);

namespace App\Authentication\User\User;

use Assert\Assertion;
use Throwable;

readonly class Email
{
    public bool $isValid;

    public string $errorMessage;

    public function __construct(public string $address)
    {
        $errorMessage = '';

        try {
            Assertion::email(
                $address,
                'A valid email address is required',
            );
        } catch (Throwable $e) {
            $errorMessage = $e->getMessage();
        }

        $this->isValid = $errorMessage === '';

        $this->errorMessage = $errorMessage;
    }
}
