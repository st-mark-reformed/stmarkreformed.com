<?php

declare(strict_types=1);

namespace App\Profiles\Admin\EditProfile\PostEditProfile;

use App\Profiles\PopulatedProfile;
use App\Profiles\ProfileResult;

readonly class UpdatedProfileFactory
{
    public function create(
        PopulatedProfile $requestProfile,
        ProfileResult $persistentProfileResult,
    ): PopulatedProfile {
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
