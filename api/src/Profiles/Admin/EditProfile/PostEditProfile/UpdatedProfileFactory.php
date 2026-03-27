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
            ->withTitleOrHonorific($requestProfile->titleOrHonorific)
            ->withFirstName($requestProfile->firstName)
            ->withLastName($requestProfile->lastName)
            ->withEmail($requestProfile->email)
            ->withLeadershipPosition($requestProfile->leadershipPosition)
            ->withBio($requestProfile->bio);
    }
}
