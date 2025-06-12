<?php

declare(strict_types=1);

namespace App\Authentication\User\Persistence;

use App\Authentication\User\User\User;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

readonly class UserTransformer
{
    public function createRecord(User $fromUser): UserRecord
    {
        $record = new UserRecord();

        $record->id = $fromUser->id->toString();

        $record->email = $fromUser->email->address;

        $record->roles = $fromUser->roles->asString();

        $record->is_active = $fromUser->isActive;

        return $record;
    }
}
