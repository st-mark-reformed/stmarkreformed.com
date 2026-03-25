<?php

declare(strict_types=1);

namespace App\Profiles\Admin\NewProfile;

use App\Profiles\NewProfile;
use App\Profiles\ProfileEmail;
use App\Profiles\ProfileLeadershipPosition;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class NewProfileFactory
{
    public function createFromRequest(ServerRequest $request): NewProfile
    {
        return new NewProfile(
            titleOrHonorific: $request->parsedBody->getString(
                name: 'titleOrHonorific',
            ),
            firstName: $request->parsedBody->getString(name: 'firstName'),
            lastName: $request->parsedBody->getString(name: 'lastName'),
            email: new ProfileEmail(email: $request->parsedBody->getString(
                name: 'email',
            )),
            leadershipPosition: ProfileLeadershipPosition::fromStringSafe(
                type: $request->parsedBody->getString(
                    name: 'leadershipPosition',
                ),
            ),
            bio: $request->parsedBody->getString(name: 'bio'),
        );
    }
}
