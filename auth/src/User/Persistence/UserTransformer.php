<?php

declare(strict_types=1);

namespace App\User\Persistence;

use App\EmptyUuid;
use App\User\User;
use App\User\UserEmail;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Throwable;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

readonly class UserTransformer
{
    public function fromRecord(UserRecord $record): User
    {
        return new User(
            id: $this->createId($record),
            email: new UserEmail($record->email),
            passwordHash:  $record->password_hash,
        );
    }

    private function createId(UserRecord $record): UuidInterface
    {
        try {
            return Uuid::fromString($record->id);
        } catch (Throwable) {
            return new EmptyUuid();
        }
    }
}
