<?php

declare(strict_types=1);

namespace App\Profiles;

readonly class ProfileResult
{
    public bool $hasProfile;

    public Profile $profile;

    public function __construct(Profile|null $profile = null)
    {
        $this->hasProfile = $profile !== null;
        $this->profile    = $profile ?? new Profile();
    }
}
