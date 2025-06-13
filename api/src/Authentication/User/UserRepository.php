<?php

declare(strict_types=1);

namespace App\Authentication\User;

use App\Authentication\User\Persistence\CreateAndPersistFactory;
use App\Authentication\User\Persistence\FindAll;
use App\Authentication\User\Persistence\FindByEmail;
use App\Authentication\User\Persistence\Persist;
use App\Authentication\User\Persistence\Transformer;
use App\Authentication\User\User\User;
use App\Authentication\User\User\Users;
use App\Persistence\Result;

readonly class UserRepository
{
    public function __construct(
        private FindAll $findAll,
        private Persist $persist,
        private Transformer $transformer,
        private FindByEmail $findByEmail,
        private CreateAndPersistFactory $createAndPersistFactory,
    ) {
    }

    public function createAndPersist(User $user): Result
    {
        return $this->createAndPersistFactory->create($user);
    }

    public function persist(User $user): Result
    {
        return $this->persist->persist($user);
    }

    public function findAll(): Users
    {
        return $this->transformer->createUsers(
            $this->findAll->find(),
        );
    }

    public function findByEmail(string $email): User|null
    {
        $record = $this->findByEmail->find($email);

        return $record !== null ?
            $this->transformer->createUser($record) :
            null;
    }
}
