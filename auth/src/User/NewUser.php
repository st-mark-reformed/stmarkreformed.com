<?php

declare(strict_types=1);

namespace App\User;

readonly class NewUser
{
    public function __construct(
        public UserEmail $email,
        public string $password = '',
        public UserRoles $roles = new UserRoles(),
    ) {
    }

    /** @return string[] */
    public function getValidationMessages(): array
    {
        if ($this->email->isValid) {
            return [];
        }

        return ['A valid email address is required.'];
    }
}
