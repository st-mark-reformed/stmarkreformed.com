<?php

declare(strict_types=1);

namespace App\User;

use App\EmptyUuid;
use Ramsey\Uuid\UuidInterface;

readonly class User
{
    public bool $isValid;

    public function __construct(
        public UuidInterface $id = new EmptyUuid(),
        public UserEmail $email = new UserEmail(),
        public string $passwordHash = '',
    ) {
        $this->isValid = ! $id instanceof EmptyUuid && $email->isValid;
    }
}
