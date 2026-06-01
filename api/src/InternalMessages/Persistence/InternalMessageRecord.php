<?php

declare(strict_types=1);

namespace App\InternalMessages\Persistence;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class InternalMessageRecord extends Record
{
    public const string TABLE_NAME = 'internal_messages';

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

    public string $title = '';

    public string $slug = '';

    public string $audio_path = '';

    public int $audio_file_size = 0;

    public string $speaker_id = '';

    public string $passage = '';

    public string $series_id = '';

    public string $description = '';
}
