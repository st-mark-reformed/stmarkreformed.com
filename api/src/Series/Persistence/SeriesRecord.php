<?php

declare(strict_types=1);

namespace App\Series\Persistence;

use App\Persistence\Record;

class SeriesRecord extends Record
{
    public const string TABLE_NAME = 'series';

    public static function getTableName(): string
    {
        return self::TABLE_NAME;
    }

    public function tableName(): string
    {
        return self::TABLE_NAME;
    }

    public string $id = '';

    public string $title = '';

    public string $slug = '';
}
