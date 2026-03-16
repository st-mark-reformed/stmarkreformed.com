<?php

declare(strict_types=1);

namespace App\User;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

readonly class UserSession
{
    public function __construct(
        public UuidInterface $id,
        public DateTimeImmutable $expires,
        public User $user,
    ) {
    }
}
