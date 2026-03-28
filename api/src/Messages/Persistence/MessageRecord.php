<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class MessageRecord extends Record
{
    public const string TABLE_NAME = 'messages';

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

    public string $speaker_id = '';

    public string $passage = '';

    public string $series_id = '';

    public string $description = '';
}
