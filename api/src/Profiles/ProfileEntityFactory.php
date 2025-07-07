<?php

declare(strict_types=1);

namespace App\Profiles;

use App\Profiles\Profile\Email;
use App\Profiles\Profile\FirstName;
use App\Profiles\Profile\LastName;
use App\Profiles\Profile\LeadershipPosition;
use App\Profiles\Profile\Profile;
use App\Profiles\Profile\Slug;
use Cocur\Slugify\Slugify;
use Psr\Http\Message\ServerRequestInterface;

use function is_array;

readonly class ProfileEntityFactory
{
    public function fromServerRequest(ServerRequestInterface $request): Profile
    {
        $submittedData = $request->getParsedBody();
        $submittedData = is_array($submittedData) ? $submittedData : [];

        $firstName = $submittedData['firstName'] ?? '';

        $lastName = $submittedData['lastName'] ?? '';

        $slug = $submittedData['slug'] ?? '';

        if ($slug === '') {
            $slug = Slugify::create()->slugify(
                $firstName . ' ' . $lastName,
            );
        }

        return new Profile(
            slug: new Slug($slug),
            firstName: new FirstName($firstName),
            lastName: new LastName($lastName),
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
