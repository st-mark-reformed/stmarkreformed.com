<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

use App\Profiles\Profile;
use App\Profiles\ProfileEmail;
use App\Profiles\ProfileLeadershipPosition;
use App\Profiles\Profiles;
use Ramsey\Uuid\Uuid;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

readonly class Transformer
{
    public function toEntity(ProfileRecord $record): Profile
    {
        return new Profile(
            id: Uuid::fromString($record->id),
            titleOrHonorific: $record->title_or_honorific,
            firstName: $record->first_name,
            lastName: $record->last_name,
            email: new ProfileEmail(email: $record->email),
            leadershipPosition: ProfileLeadershipPosition::fromString(
                type: $record->leadership_position,
            ),
            bio: $record->bio,
            hasMessages: $record->has_messages,
        );
    }

    public function toEntities(ProfileRecords $records): Profiles
    {
        return new Profiles(
            profiles: $records->map(
                callback: fn (ProfileRecord $r) => $this->toEntity($r),
            ),
        );
    }
}
