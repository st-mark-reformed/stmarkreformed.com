<?php

declare(strict_types=1);

namespace Cli\Commands\SyncFromProd;

readonly class FilesDirectoryMapping
{
    public function __construct(
        public string $sourceSubPath,
        public string $localDestRelativeToProjectRoot,
    ) {
    }
}
