<?php

declare(strict_types=1);

namespace App\Authentication\User\Persistence;

use App\Authentication\User\User\Email;
use App\Authentication\User\User\Role;
use App\Authentication\User\User\Roles;
use App\Authentication\User\User\User;
use App\Authentication\User\User\Users;
use Ramsey\Uuid\Uuid;

use function array_map;
use function is_array;
use function json_decode;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

readonly class Transformer
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

    public function createUser(UserRecord $fromRecord): User
    {
        $roles = json_decode($fromRecord->roles, true);
        $roles = is_array($roles) ? $roles : [];
        $roles = array_map(
            static fn (string $r) => Role::createFromName($r),
            $roles,
        );

        return new User(
            new Email($fromRecord->email),
            new Roles($roles),
            $fromRecord->is_active,
            Uuid::fromString($fromRecord->id),
        );
    }

    public function createUsers(UserRecords $fromRecords): Users
    {
        return new Users($fromRecords->mapToArray(
            fn (UserRecord $r) => $this->createUser($r),
        ));
    }
}
