<?php

declare(strict_types=1);

namespace App\Profiles;

use App\Profiles\Persistence\CreateProfile;
use App\Result;

readonly class ProfilesRepository
{
    public function __construct(
        private CreateProfile $createProfile,
    ) {
    }

    public function createProfile(NewProfile $newProfile): Result
    {
        return $this->createProfile->create(newProfile: $newProfile);
    }
}
