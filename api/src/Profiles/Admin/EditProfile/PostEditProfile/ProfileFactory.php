<?php

declare(strict_types=1);

namespace App\Profiles\Admin\EditProfile\PostEditProfile;

use App\EmptyUuid;
use App\Profiles\Profile;
use App\Profiles\ProfileEmail;
use App\Profiles\ProfileLeadershipPosition;
use Ramsey\Uuid\Uuid;
use RxAnte\AppBootstrap\Request\ServerRequest;
use Throwable;

readonly class ProfileFactory
{
    public function createFromRequest(ServerRequest $request): Profile
    {
        try {
            $id = Uuid::fromString(
                $request->attributes->getString('profileId'),
            );
        } catch (Throwable) {
            $id = new EmptyUuid();
        }

        return new Profile(
            id: $id,
            titleOrHonorific: $request->parsedBody->getString(
                'titleOrHonorific',
            ),
            firstName: $request->parsedBody->getString(name: 'firstName'),
            lastName: $request->parsedBody->getString(name: 'lastName'),
            email: new ProfileEmail(
                email: $request->parsedBody->getString(name: 'email'),
            ),
            leadershipPosition: ProfileLeadershipPosition::fromString(
                type: $request->parsedBody->getString(
                    name: 'leadershipPosition',
                ),
            ),
            bio: $request->parsedBody->getString(name: 'bio'),
        );
    }
}
