<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types=1);

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Migration\AbstractMigration;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch

class CreateNewsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table(
            'news',
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
            'heading',
            AdapterInterface::PHINX_TYPE_STRING,
            ['null' => false, 'default' => ''],
        )->addColumn(
            'subheading',
            AdapterInterface::PHINX_TYPE_STRING,
            ['null' => false, 'default' => ''],
        )->addColumn(
            'body',
            AdapterInterface::PHINX_TYPE_TEXT,
            ['null' => false, 'default' => ''],
        )
            ->addIndex(['enabled'])
            ->addIndex(['date'])
            ->addIndex(['title'])
            ->addIndex(['slug'])
            ->create();
    }
}
