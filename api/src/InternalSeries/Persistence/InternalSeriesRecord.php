<?php

declare(strict_types=1);

namespace App\InternalSeries\Persistence;

use App\Persistence\Record;

class InternalSeriesRecord extends Record
{
    public const string TABLE_NAME = 'internal_series';

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
