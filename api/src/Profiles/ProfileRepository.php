<?php

declare(strict_types=1);

namespace App\Profiles;

use App\Persistence\Result;
use App\Profiles\Persistence\CreateAndPersistFactory;
use App\Profiles\Profile\Profile;

readonly class ProfileRepository
{
    public function __construct(
        private CreateAndPersistFactory $createAndPersistFactory,
    ) {
    }

    public function createAndPersist(Profile $profile): Result
    {
        return $this->createAndPersistFactory->create($profile);
    }
}
