<?php

declare(strict_types=1);

namespace App\Authentication\User;

use App\Authentication\User\Persistence\CreateAndPersistUserFactory;
use App\Authentication\User\Persistence\FindAllUsers;
use App\Authentication\User\Persistence\UserTransformer;
use App\Authentication\User\User\User;
use App\Authentication\User\User\Users;
use App\Persistence\Result;

readonly class UserRepository
{
    public function __construct(
        private FindAllUsers $findAllUsers,
        private UserTransformer $transformer,
        private CreateAndPersistUserFactory $createAndPersistUser,
    ) {
    }

    public function createAndPersistUser(User $user): Result
    {
        return $this->createAndPersistUser->create($user);
    }

    public function findAllUsers(): Users
    {
        return $this->transformer->createUsers(
            $this->findAllUsers->find(),
        );
    }
}
