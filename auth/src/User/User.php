<?php

declare(strict_types=1);

namespace App\User;

use App\EmptyUuid;
use Ramsey\Uuid\UuidInterface;

use function password_verify;

readonly class User
{
    public bool $isValid;

    public function __construct(
        public UuidInterface $id = new EmptyUuid(),
        public UserEmail $email = new UserEmail(),
        public string $passwordHash = '',
        public UserRoles $roles = new UserRoles(),
    ) {
        $this->isValid = ! $id instanceof EmptyUuid && $email->isValid;
    }

    public function isPasswordValid(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }
}
