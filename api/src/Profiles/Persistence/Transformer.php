<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

use App\Profiles\Profile\Profile;

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
}
