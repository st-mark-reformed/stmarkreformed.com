<?php

declare(strict_types=1);

namespace App\MenOfTheMark\Persistence;

use App\Persistence\Record;

class MenOfTheMarkItemRecord extends Record
{
    public const string TABLE_NAME = 'men_of_the_mark';

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
}
