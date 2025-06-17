<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types=1);

use App\Messages\Series\Persistence\MessageSeriesTable;
use Phinx\Migration\AbstractMigration;

// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class CreateMessageSeriesTable extends AbstractMigration
{
    public function change(): void
    {
        MessageSeriesTable::createSchema($this)->create();
    }
}
