<?php

declare(strict_types=1);

namespace App\LogIn;

use function array_map;
use function filter_var;

use const FILTER_VALIDATE_EMAIL;

readonly class PostData
{
    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    public function __construct(
        public string $redirectUrl,
        public string $email,
        public string $password,
    ) {
        $isValid = true;

        $validationMessages = [];

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $validationMessages[] = 'A valid email address is required.';

            $isValid = false;
        }

        if ($password === '') {
            $validationMessages[] = 'A password is required.';

            $isValid = false;
        }

        $this->isValid = $isValid;

        $this->validationMessages = $validationMessages;
    }

    public function walkValidationMessages(callable $callback): void
    {
        array_map($callback, $this->validationMessages);
    }
}
