<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Persistence;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class HymnOfTheMonthItemRecord extends Record
{
    public const string TABLE_NAME = 'hymns_of_the_month';

    public static function getTableName(): string
    {
        return self::TABLE_NAME;
    }

    public function tableName(): string
    {
        return self::TABLE_NAME;
    }

    public string $id = '';

    public bool $enabled = true;

    public string $date = '';

    public string $slug = '';

    public string $hymn_psalm_name = '';

    public string $music_sheet_path = '';

    public string $practice_tracks = '';
}
