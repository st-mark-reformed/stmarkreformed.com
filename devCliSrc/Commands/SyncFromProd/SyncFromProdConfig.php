<?php

declare(strict_types=1);

namespace Cli\Commands\SyncFromProd;

use InvalidArgumentException;

readonly class SyncFromProdConfig
{
    public bool $syncDatabases;
    public bool $syncFiles;

    public function __construct(
        bool $dbOnly = false,
        bool $filesOnly = false,
    ) {
        if ($dbOnly && $filesOnly) {
            throw new InvalidArgumentException(
                'Cannot specify both --db-only and --files-only',
            );
        }

        $this->syncDatabases = ! $filesOnly;
        $this->syncFiles     = ! $dbOnly;
    }
}
