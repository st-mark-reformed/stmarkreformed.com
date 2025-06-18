<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class MessageRecord extends Record
{
    public static function getTableName(): string
    {
        return MessagesTable::TABLE_NAME;
    }

    public function tableName(): string
    {
        return MessagesTable::TABLE_NAME;
    }

    public string $id = '';

    public bool $is_published = false;

    public string $date = '';

    public string $title = '';

    public string $text = '';

    public string $speaker_profile_id = '';

    public string $series_id = '';

    public string $audio_file_name = '';
}
