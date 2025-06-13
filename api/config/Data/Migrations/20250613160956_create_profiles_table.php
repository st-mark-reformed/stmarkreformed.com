<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types=1);

use App\Profiles\Persistence\ProfilesTable;
use Phinx\Migration\AbstractMigration;

// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class CreateProfilesTable extends AbstractMigration
{
    public function change(): void
    {
        ProfilesTable::createSchema($this)->create();
    }
}
