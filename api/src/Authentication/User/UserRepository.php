<?php

declare(strict_types=1);

namespace App\Authentication\User;

use App\Authentication\User\Persistence\CreateAndPersistUserFactory;
use App\Authentication\User\User\User;
use App\Persistence\Result;

readonly class UserRepository
{
    public function __construct(
        private CreateAndPersistUserFactory $createAndPersistUser,
    ) {
    }

    public function createAndPersistUser(User $user): Result
    {
        return $this->createAndPersistUser->create($user);
    }
}
