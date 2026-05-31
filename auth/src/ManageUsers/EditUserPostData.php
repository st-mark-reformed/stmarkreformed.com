<?php

declare(strict_types=1);

namespace App\ManageUsers;

use App\User\UserEmail;

use function array_map;

readonly class EditUserPostData
{
    public UserEmail $email;

    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    /** @param string[] $roleNames */
    public function __construct(
        string $email,
        public array $roleNames,
    ) {
        $this->email = new UserEmail($email);

        $isValid = true;

        $validationMessages = [];

        if (! $this->email->isValid) {
            $validationMessages[] = 'A valid email address is required.';

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
