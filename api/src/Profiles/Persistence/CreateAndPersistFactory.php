<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

use App\Persistence\PersistNewRecord;
use App\Persistence\Result;
use App\Profiles\Profile\Profile;

readonly class CreateAndPersistFactory
{
    public function __construct(
        private Transformer $transformer,
        private PersistNewRecord $persistNewRecord,
    ) {
    }

    public function create(Profile $profile): Result
    {
        if (! $profile->isValid) {
            return new Result(
                false,
                $profile->errorMessages,
            );
        }

        $profileRecord = $this->transformer->createRecord($profile);

        return $this->persistNewRecord->persist($profileRecord);
    }
}
