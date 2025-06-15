<?php

declare(strict_types=1);

namespace App\Profiles;

use App\Persistence\Result;
use App\Profiles\Persistence\CreateAndPersistFactory;
use App\Profiles\Persistence\FindAll;
use App\Profiles\Persistence\Transformer;
use App\Profiles\Profile\Profile;
use App\Profiles\Profile\Profiles;

readonly class ProfileRepository
{
    public function __construct(
        private FindAll $findAll,
        private Transformer $transformer,
        private CreateAndPersistFactory $createAndPersistFactory,
    ) {
    }

    public function createAndPersist(Profile $profile): Result
    {
        return $this->createAndPersistFactory->create($profile);
    }

    public function findAll(): Profiles
    {
        return $this->transformer->createProfiles(
            $this->findAll->find(),
        );
    }
}
