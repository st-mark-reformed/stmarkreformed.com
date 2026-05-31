<?php

declare(strict_types=1);

namespace App\User;

use App\User\Persistence\CreateUser;
use App\User\Persistence\DeleteUserById;
use App\User\Persistence\FindAllUsers;
use App\User\Persistence\FindUserByEmail;
use App\User\Persistence\FindUserById;
use App\User\Persistence\UpdateUser;
use App\User\Persistence\UserTransformer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

readonly class UserRepository implements UserUpdater
{
    public function __construct(
        private CreateUser $createUser,
        private UpdateUser $updateUser,
        private DeleteUserById $deleteUserById,
        private FindAllUsers $findAllUsers,
        private FindUserByEmail $findUserByEmail,
        private FindUserById $findUserById,
        private UserTransformer $userTransformer,
    ) {
    }

    public function all(): Users
    {
        return $this->findAllUsers->find();
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

    public function findById(UuidInterface|string $id): User
    {
        if ($id instanceof UuidInterface) {
            $id = $id->toString();
        }

        return $this->userTransformer->fromRecord(
            $this->findUserById->find($id),
        );
    }

    public function deleteUserById(UuidInterface|string $id): void
    {
        if (! $id instanceof UuidInterface) {
            $id = Uuid::fromString($id);
        }

        $this->deleteUserById->delete($id);
    }
}
