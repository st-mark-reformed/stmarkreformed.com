<?php

declare(strict_types=1);

namespace App\Profiles\Profile;

use Assert\Assertion;
use Throwable;

readonly class Email
{
    public bool $isValid;

    public string $errorMessage;

    public function __construct(public string $address)
    {
        if ($address === '') {
            $this->isValid = true;

            $this->errorMessage = '';

            return;
        }

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
