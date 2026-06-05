<?php

declare(strict_types=1);

namespace App\Resources\Persistence;

use App\Persistence\Record;

class ResourceItemRecord extends Record
{
    public const string TABLE_NAME = 'resources';

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

    public string $body = '';

    public string $downloads = '';
}
