<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types=1);

use App\Authentication\User\Persistence\UsersTable;
use Phinx\Migration\AbstractMigration;

// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class CreateUsersTable extends AbstractMigration
{
    public function change(): void
    {
        UsersTable::createSchema($this)->create();
    }
}
