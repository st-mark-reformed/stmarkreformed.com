<?php

declare(strict_types=1);

namespace App\Authentication\User\Persistence;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class UserRecord extends Record
{
    public static function getTableName(): string
    {
        return UsersTable::TABLE_NAME;
    }

    public function tableName(): string
    {
        return UsersTable::TABLE_NAME;
    }

    public string $id = '';

    public string $email = '';

    public string $roles = '';

    public bool $is_active = false;
}
