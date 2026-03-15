<?php

declare(strict_types=1);

namespace App\User;

use App\User\Persistence\FindUserByEmail;
use App\User\Persistence\UserTransformer;

readonly class UserRepository
{
    public function __construct(
        private FindUserByEmail $findUserByEmail,
        private UserTransformer $userTransformer,
    ) {
    }

    public function findByEmail(string $email): User
    {
        return $this->userTransformer->fromRecord(
            $this->findUserByEmail->find($email),
        );
    }
}
