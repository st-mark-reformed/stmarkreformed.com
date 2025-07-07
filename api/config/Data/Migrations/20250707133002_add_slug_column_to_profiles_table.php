<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types=1);

use App\Profiles\Persistence\ProfilesTable;
use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Migration\AbstractMigration;

// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class AddSlugColumnToProfilesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table(ProfilesTable::TABLE_NAME);

        if ($table->hasColumn('slug')) {
            return;
        }

        $table->addColumn(
            'slug',
            AdapterInterface::PHINX_TYPE_STRING,
            [
                'null' => false,
                'default' => '',
                'after' => 'id',
            ],
        )->save();
    }
}
