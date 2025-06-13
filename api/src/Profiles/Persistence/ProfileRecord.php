<?php

declare(strict_types=1);

namespace App\Profiles\Persistence;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class ProfileRecord extends Record
{
    public static function getTableName(): string
    {
        return ProfilesTable::TABLE_NAME;
    }

    public function tableName(): string
    {
        return ProfilesTable::TABLE_NAME;
    }

    public string $id = '';

    public string $first_name = '';

    public string $last_name = '';

    public string $title_or_honorific = '';

    public string $email = '';

    public string $leadership_position = '';
}
