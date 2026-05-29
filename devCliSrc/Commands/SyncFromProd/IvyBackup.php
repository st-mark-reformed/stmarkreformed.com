<?php

declare(strict_types=1);

namespace Cli\Commands\SyncFromProd;

use function ltrim;
use function rtrim;

readonly class IvyBackup
{
    public function __construct(
        public string $sshHost,
        public string $remoteBackupPath,
        public string $timestamp,
    ) {
    }

    public function remotePathFor(string $subPath): string
    {
        return rtrim($this->remoteBackupPath, '/')
            . '/'
            . ltrim($subPath, '/');
    }

    public function sshSourceFor(string $subPath): string
    {
        return $this->sshHost . ':' . $this->remotePathFor($subPath);
    }
}
