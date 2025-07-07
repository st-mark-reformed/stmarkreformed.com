<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

use App\Profiles\Profile\Email;
use App\Profiles\Profile\FirstName;
use App\Profiles\Profile\LastName;
use App\Profiles\Profile\LeadershipPosition;
use App\Profiles\Profile\Profile;
use App\Profiles\Profile\Profiles;
use App\Profiles\Profile\Slug;
use Ramsey\Uuid\Uuid;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

readonly class Transformer
{
    public function createRecord(Profile $fromProfile): ProfileRecord
    {
        $record = new ProfileRecord();

        $record->id = $fromProfile->id->toString();

        $record->slug = $fromProfile->slug->slug;

        $record->first_name = $fromProfile->firstName->firstName;

        $record->last_name = $fromProfile->lastName->lastName;

        $record->title_or_honorific = $fromProfile->titleOrHonorific;

        $record->email = $fromProfile->email->address;

        $record->leadership_position = $fromProfile->leadershipPosition->name;

        return $record;
    }

    public function createProfile(ProfileRecord $fromRecord): Profile
    {
        return new Profile(
            slug: new Slug($fromRecord->slug),
            firstName: new FirstName($fromRecord->first_name),
            lastName: new LastName($fromRecord->last_name),
            titleOrHonorific: $fromRecord->title_or_honorific,
            email: new Email($fromRecord->email),
            leadershipPosition: LeadershipPosition::createFromString(
                $fromRecord->leadership_position,
            ),
            id: Uuid::fromString($fromRecord->id),
        );
    }

    public function createProfiles(ProfileRecords $fromRecords): Profiles
    {
        return new Profiles($fromRecords->mapToArray(
            fn (ProfileRecord $record) => $this->createProfile(
                $record,
            ),
        ));
    }
}
