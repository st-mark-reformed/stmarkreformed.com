<?php

declare(strict_types=1);

namespace App\ManageUsers;

use App\User\UserEmail;

use function array_map;
use function mb_strlen;

readonly class CreateUserPostData
{
    public UserEmail $email;

    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    /** @param string[] $roleNames */
    public function __construct(
        string $email,
        public string $password,
        public string $confirmPassword,
        public array $roleNames,
    ) {
        $this->email = new UserEmail($email);

        $isValid = true;

        $validationMessages = [];

        if (! $this->email->isValid) {
            $validationMessages[] = 'A valid email address is required.';

            $isValid = false;
        }

        if (mb_strlen($password) < 8) {
            $validationMessages[] = 'The password must be at least 8 characters long.';

            $isValid = false;
        }

        if ($password !== $confirmPassword) {
            $validationMessages[] = 'The password and confirmation do not match.';

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
