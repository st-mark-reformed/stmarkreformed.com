<?php

declare(strict_types=1);

namespace App\Authentication\User\Persistence;

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Db\Table;
use Phinx\Migration\MigrationInterface;

readonly class UsersTable
{
    public const string TABLE_NAME = 'users';

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
            'email',
            AdapterInterface::PHINX_TYPE_STRING,
            ['null' => false, 'default' => ''],
        )->addColumn(
            'roles',
            AdapterInterface::PHINX_TYPE_JSON,
        )->addColumn(
            'is_active',
            AdapterInterface::PHINX_TYPE_BOOLEAN,
            ['default' => 1],
        )
            ->addIndex(['email'])
            ->addIndex(['is_active'])
            ->addIndex(['roles']);
    }
}
