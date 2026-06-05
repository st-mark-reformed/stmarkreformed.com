<?php

declare(strict_types=1);

namespace App\MailingLists\Persistence;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class SubscriberRecord extends Record
{
    public const string TABLE_NAME = 'mailing_list_subscribers';

    public static function getTableName(): string
    {
        return self::TABLE_NAME;
    }

    public function tableName(): string
    {
        return self::TABLE_NAME;
    }

    public string $id = '';

    public string $mailing_list_id = '';

    public string $name = '';

    public string $email_address = '';
}
