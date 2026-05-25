<?php

declare(strict_types=1);

namespace App\Profiles;

readonly class ProfileResult
{
    public bool $hasProfile;

    public PopulatedProfile $profile;

    public function __construct(PopulatedProfile|null $profile = null)
    {
        $this->hasProfile = $profile !== null;
        $this->profile    = $profile ?? new PopulatedProfile();
    }
}
