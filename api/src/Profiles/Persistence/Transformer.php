<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

use App\Profiles\Profile\Email;
use App\Profiles\Profile\FirstName;
use App\Profiles\Profile\LastName;
use App\Profiles\Profile\LeadershipPosition;
use App\Profiles\Profile\Profile;
use App\Profiles\Profile\Profiles;
use Ramsey\Uuid\Uuid;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

readonly class Transformer
{
    public function createRecord(Profile $fromProfile): ProfileRecord
    {
        $record = new ProfileRecord();

        $record->id = $fromProfile->id->toString();

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
            new FirstName($fromRecord->first_name),
            new LastName($fromRecord->last_name),
            $fromRecord->title_or_honorific,
            new Email($fromRecord->email),
            LeadershipPosition::createFromString(
                $fromRecord->leadership_position,
            ),
            Uuid::fromString($fromRecord->id),
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
