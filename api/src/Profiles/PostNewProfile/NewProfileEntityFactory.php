<?php

declare(strict_types=1);

namespace App\Profiles\PostNewProfile;

use App\Profiles\Profile\Email;
use App\Profiles\Profile\FirstName;
use App\Profiles\Profile\LastName;
use App\Profiles\Profile\LeadershipPosition;
use App\Profiles\Profile\Profile;
use Psr\Http\Message\ServerRequestInterface;

use function is_array;

readonly class NewProfileEntityFactory
{
    public function fromServerRequest(ServerRequestInterface $request): Profile
    {
        $submittedData = $request->getParsedBody();
        $submittedData = is_array($submittedData) ? $submittedData : [];

        return new Profile(
            firstName: new FirstName(
                $submittedData['firstName'] ?? '',
            ),
            lastName: new LastName(
                $submittedData['lastName'] ?? '',
            ),
            titleOrHonorific: $submittedData['titleOrHonorific'] ?? '',
            email: new Email(
                $submittedData['email'] ?? '',
            ),
            leadershipPosition: LeadershipPosition::createFromString(
                $submittedData['leadershipPosition'] ?? '',
            ),
        );
    }
}
