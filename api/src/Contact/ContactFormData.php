<?php

declare(strict_types=1);

namespace App\Contact;

use function count;
use function filter_var;

use const FILTER_VALIDATE_EMAIL;

readonly class ContactFormData
{
    /** @var string[] */
    public array $errors;

    public function __construct(
        string $aPassword,
        string $yourCompany,
        public string $name,
        public string $emailAddress,
        public string $message,
    ) {
        $errors = [];

        if ($aPassword !== '' || $yourCompany !== '') {
            $errors[] = 'An unknown error occurred';
        }

        if ($name === '') {
            $errors[] = 'Your name is required';
        }

        if (
            filter_var(
                $emailAddress,
                FILTER_VALIDATE_EMAIL,
            ) === false
        ) {
            $errors[] = 'A valid email address is required';
        }

        if ($message === '') {
            $errors[] = 'A message is required';
        }

        $this->errors = $errors;
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }
}
