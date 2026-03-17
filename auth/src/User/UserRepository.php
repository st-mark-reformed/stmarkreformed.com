<?php

declare(strict_types=1);

namespace App\User;

use App\User\Persistence\CreateUser;
use App\User\Persistence\FindUserByEmail;
use App\User\Persistence\UpdateUser;
use App\User\Persistence\UserTransformer;

readonly class UserRepository
{
    public function __construct(
        private CreateUser $createUser,
        private UpdateUser $updateUser,
        private FindUserByEmail $findUserByEmail,
        private UserTransformer $userTransformer,
    ) {
    }

    public function createUser(NewUser $newUser): Result
    {
        return $this->createUser->create($newUser);
    }

    public function updateUser(User $user): Result
    {
        return $this->updateUser->update($user);
    }

    public function findByEmail(string $email): User
    {
        return $this->userTransformer->fromRecord(
            $this->findUserByEmail->find($email),
        );
    }
}
