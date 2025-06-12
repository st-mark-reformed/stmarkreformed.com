<?php

declare(strict_types=1);

namespace App\Authentication\User\Persistence;

use App\Authentication\User\User\User;
use App\Persistence\PersistRecord;
use App\Persistence\Result;

readonly class Persist
{
    public function __construct(
        private Transformer $transformer,
        private PersistRecord $persistRecord,
    ) {
    }

    public function persist(User $user): Result
    {
        $record = $this->transformer->createRecord($user);

        return $this->persistRecord->persist($record);
    }
}
