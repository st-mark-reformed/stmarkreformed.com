<?php

declare(strict_types=1);

namespace App\Authentication\User\Persistence;

use App\Authentication\User\User\User;
use App\Persistence\PersistNewRecord;
use App\Persistence\Result;

readonly class CreateAndPersistUserFactory
{
    public function __construct(
        private UserTransformer $transformer,
        private PersistNewRecord $persistNewRecord,
    ) {
    }

    public function create(User $user): Result
    {
        if (! $user->isValid) {
            return new Result(
                false,
                $user->errorMessages,
            );
        }

        // TODO: Check for existing email address

        $userRecord = $this->transformer->createRecord($user);

        return $this->persistNewRecord->persist($userRecord);
    }
}
