<?php

declare(strict_types=1);

namespace Cli\Commands\SyncFromProd;

readonly class DatabaseDumpMapping
{
    public function __construct(
        public string $sourceFile,
        public string $targetDatabase,
    ) {
    }
}
