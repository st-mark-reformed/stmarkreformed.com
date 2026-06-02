<?php

declare(strict_types=1);

namespace App\News\Persistence;

use App\Persistence\Record;

class NewsItemRecord extends Record
{
    public const string TABLE_NAME = 'news';

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

    public string $heading = '';

    public string $subheading = '';

    public string $body = '';
}
