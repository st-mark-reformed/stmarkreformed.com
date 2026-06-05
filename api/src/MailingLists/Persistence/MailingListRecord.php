<?php

declare(strict_types=1);

namespace App\MailingLists\Persistence;

use App\Persistence\Record;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

class MailingListRecord extends Record
{
    public const string TABLE_NAME = 'mailing_lists';

    public static function getTableName(): string
    {
        return self::TABLE_NAME;
    }

    public function tableName(): string
    {
        return self::TABLE_NAME;
    }

    public string $id = '';

    public string $list_name = '';

    public string $list_address = '';

    public string $imap_server = '';

    public int $imap_port = 993;

    public string $connection_type = 'ssl';

    public string $username = '';

    public string $password = '';
}
