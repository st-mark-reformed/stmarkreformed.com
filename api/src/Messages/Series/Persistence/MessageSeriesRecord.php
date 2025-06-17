<?php

declare(strict_types=1);

namespace App\Messages\Series\Persistence;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

use App\Persistence\Record;

class MessageSeriesRecord extends Record
{
    public static function getTableName(): string
    {
        return MessageSeriesTable::TABLE_NAME;
    }

    public function tableName(): string
    {
        return MessageSeriesTable::TABLE_NAME;
    }

    public string $id = '';

    public string $title = '';

    public string $slug = '';
}
