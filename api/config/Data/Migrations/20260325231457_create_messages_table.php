<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types=1);

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Migration\AbstractMigration;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch

class CreateMessagesTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table(
            'messages',
            [
                'id' => false,
                'primary_key' => ['id'],
            ],
        )->addColumn(
            'id',
            AdapterInterface::PHINX_TYPE_UUID,
        )->addColumn(
            'enabled',
            AdapterInterface::PHINX_TYPE_BOOLEAN,
            ['null' => false, 'default' => true],
        )->addColumn(
            'date',
            AdapterInterface::PHINX_TYPE_DATETIME,
            ['default' => null],
        )->addColumn(
            'title',
            AdapterInterface::PHINX_TYPE_STRING,
            ['null' => false, 'default' => ''],
        )->addColumn(
            'slug',
            AdapterInterface::PHINX_TYPE_STRING,
            ['null' => false, 'default' => ''],
        )->addColumn(
            'audio_path',
            AdapterInterface::PHINX_TYPE_STRING,
            ['null' => false, 'default' => ''],
        )->addColumn(
            'speaker_id',
            AdapterInterface::PHINX_TYPE_UUID,
            ['null' => false, 'default' => ''],
        )->addColumn(
            'passage',
            AdapterInterface::PHINX_TYPE_STRING,
            ['null' => false, 'default' => ''],
        )->addColumn(
            'series_id',
            AdapterInterface::PHINX_TYPE_UUID,
            ['null' => false, 'default' => ''],
        )->addColumn(
            'description',
            AdapterInterface::PHINX_TYPE_TEXT,
            ['null' => false, 'default' => ''],
        )
            ->addIndex(['enabled'])
            ->addIndex(['date'])
            ->addIndex(['title'])
            ->addIndex(['slug'])
            ->addIndex(['speaker_id'])
            ->addIndex(['passage'])
            ->addIndex(['series_id'])
            ->create();
    }
}
