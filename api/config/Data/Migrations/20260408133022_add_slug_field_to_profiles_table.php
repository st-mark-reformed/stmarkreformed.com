<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types=1);

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Migration\AbstractMigration;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch

class AddSlugFieldToProfilesTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('profiles')
            ->addColumn(
                'slug',
                AdapterInterface::PHINX_TYPE_STRING,
                [
                    'null' => false,
                    'default' => '',
                    'after' => 'last_name',
                ],
            )
            ->addIndex(['slug'])
            ->update();
    }
}
