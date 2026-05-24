<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types=1);

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Migration\AbstractMigration;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch

class AddAudioFileSizeToMessagesTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('messages')
            ->addColumn(
                'audio_file_size',
                AdapterInterface::PHINX_TYPE_INTEGER,
                [
                    'null' => false,
                    'default' => 0,
                    'signed' => false,
                    'after' => 'audio_path',
                ],
            )
            ->update();
    }
}
