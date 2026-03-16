<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types=1);

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Migration\AbstractMigration;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch

class CreateUserRolesTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table(
            'user_roles',
            [
                'id' => false,
                'primary_key' => ['user_id', 'role'],
            ],
        )->addColumn(
            'user_id',
            AdapterInterface::PHINX_TYPE_UUID,
        )->addColumn(
            'role',
            AdapterInterface::PHINX_TYPE_STRING,
            ['null' => false],
        )->create();
    }
}
