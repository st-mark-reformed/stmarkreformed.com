<?php

declare(strict_types=1);

namespace App\User\Persistence;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class UserRecord extends Record
{
    public const string TABLE_NAME = 'users';

    public static function getTableName(): string
    {
        return self::TABLE_NAME;
    }

    public function tableName(): string
    {
        return self::TABLE_NAME;
    }

    public string $id = '';

    public string $email = '';

    public string $password_hash = '';
}
