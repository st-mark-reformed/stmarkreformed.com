<?php

declare(strict_types=1);

namespace App\Messages\Persistence;

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Db\Table;
use Phinx\Migration\MigrationInterface;

readonly class MessagesTable
{
    public const string TABLE_NAME = 'messages';

    public static function createSchema(MigrationInterface $migration): Table
    {
        return $migration->table(
            self::TABLE_NAME,
            [
                'id' => false,
                'primary_key' => ['id'],
            ],
        )->addColumn(
            'id',
            AdapterInterface::PHINX_TYPE_UUID,
        )->addColumn(
            'is_published',
            AdapterInterface::PHINX_TYPE_BOOLEAN,
        )->addColumn(
            'date',
            AdapterInterface::PHINX_TYPE_DATETIME,
            ['null' => true, 'default' => null],
        )->addColumn(
            'title',
            AdapterInterface::PHINX_TYPE_STRING,
            ['null' => false, 'default' => ''],
        )->addColumn(
            'text',
            AdapterInterface::PHINX_TYPE_STRING,
            ['null' => false, 'default' => ''],
        )->addColumn(
            'speaker_profile_id',
            AdapterInterface::PHINX_TYPE_UUID,
            ['null' => false, 'default' => ''],
        )->addColumn(
            'series_id',
            AdapterInterface::PHINX_TYPE_UUID,
            ['null' => false, 'default' => ''],
        )->addColumn(
            'audio_file_name',
            AdapterInterface::PHINX_TYPE_STRING,
            ['null' => false, 'default' => ''],
        )
            ->addIndex(['is_published'])
            ->addIndex(['date'])
            ->addIndex(['title'])
            ->addIndex(['text'])
            ->addIndex(['speaker_profile_id'])
            ->addIndex(['series_id'])
            ->addIndex(['audio_file_name']);
    }
}
