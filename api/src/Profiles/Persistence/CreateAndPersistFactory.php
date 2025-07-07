<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

use App\Persistence\PersistNewRecord;
use App\Persistence\PersistRecord;
use App\Persistence\Result;
use App\Profiles\Profile\Profile;

readonly class CreateAndPersistFactory
{
    public function __construct(
        private FindBySlug $findBySlug,
        private Transformer $transformer,
        private PersistRecord $persistRecord,
        private PersistNewRecord $persistNewRecord,
    ) {
    }

    public function create(Profile $profile): Result
    {
        $profile = $this->validate($profile);

        if (! $profile->isValid) {
            return new Result(
                false,
                $profile->errorMessages,
            );
        }

        $profileRecord = $this->transformer->createRecord($profile);

        return $this->persistNewRecord->persist($profileRecord);
    }

    public function persist(Profile $profile): Result
    {
        $profile = $this->validate($profile);

        if (! $profile->isValid) {
            return new Result(
                false,
                $profile->errorMessages,
            );
        }

        $profileRecord = $this->transformer->createRecord($profile);

        return $this->persistRecord->persist($profileRecord);
    }

    private function validate(Profile $profile): Profile
    {
        $existingSlug = $this->findBySlug->find(
            $profile->slug,
            $profile->id,
        );

        if ($existingSlug !== null) {
            $profile = $profile->withErrorMessage(
                'Specified slug already exists. Profile slug must be unique',
            );
        }

        return $profile;
    }
}
