<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types=1);

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Migration\AbstractMigration;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch

class CreateHymnsOfTheMonthTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table(
            'hymns_of_the_month',
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
            'slug',
            AdapterInterface::PHINX_TYPE_STRING,
            ['null' => false, 'default' => ''],
        )->addColumn(
            'hymn_psalm_name',
            AdapterInterface::PHINX_TYPE_TEXT,
            ['null' => false, 'default' => ''],
        )->addColumn(
            'music_sheet_path',
            AdapterInterface::PHINX_TYPE_STRING,
            ['null' => false, 'default' => ''],
        )->addColumn(
            'practice_tracks',
            AdapterInterface::PHINX_TYPE_TEXT,
            ['null' => false, 'default' => ''],
        )
            ->addIndex(['enabled'])
            ->addIndex(['date'])
            ->addIndex(['slug'])
            ->create();
    }
}
