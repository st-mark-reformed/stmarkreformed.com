<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types=1);

use App\Messages\Persistence\MessagesTable;
use Phinx\Migration\AbstractMigration;

// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class CreateMessagesTable extends AbstractMigration
{
    public function change(): void
    {
        MessagesTable::createSchema($this)->create();
    }
}
