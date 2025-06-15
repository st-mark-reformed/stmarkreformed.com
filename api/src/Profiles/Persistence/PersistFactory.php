<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

use App\Persistence\PersistRecord;
use App\Persistence\Result;
use App\Profiles\Profile\Profile;

readonly class PersistFactory
{
    public function __construct(
        private Transformer $transformer,
        private PersistRecord $persistRecord,
    ) {
    }

    public function persist(Profile $profile): Result
    {
        if (! $profile->isValid) {
            return new Result(
                false,
                $profile->errorMessages,
            );
        }

        $profileRecord = $this->transformer->createRecord($profile);

        return $this->persistRecord->persist($profileRecord);
    }
}
