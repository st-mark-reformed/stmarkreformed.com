<?php

declare(strict_types=1);

namespace App\Profiles\Admin\EditProfile\PostEditProfile;

use App\Profiles\Profile;
use App\Profiles\ProfileResult;

readonly class UpdatedProfileFactory
{
    public function create(
        Profile $requestProfile,
        ProfileResult $persistentProfileResult,
    ): Profile {
        if (! $persistentProfileResult->hasProfile) {
            return $persistentProfileResult->profile;
        }

        return $persistentProfileResult->profile
            ->withTitleOrHonorific(value: $requestProfile->titleOrHonorific)
            ->withFirstName(value: $requestProfile->firstName)
            ->withLastName(value: $requestProfile->lastName)
            ->withEmail(value: $requestProfile->email)
            ->withLeadershipPosition(value: $requestProfile->leadershipPosition)
            ->withBio(value: $requestProfile->bio);
    }
}
