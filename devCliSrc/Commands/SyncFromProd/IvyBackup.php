<?php

declare(strict_types=1);

namespace Cli\Commands\SyncFromProd;

use function ltrim;
use function rtrim;

readonly class IvyBackup
{
    /**
     * @param string|null $sshHost Null when the backup is on this machine's
     *                             file system (i.e. we're running on Ivy)
     */
    public function __construct(
        public string|null $sshHost,
        public string $backupPath,
        public string $timestamp,
    ) {
    }

    public function pathFor(string $subPath): string
    {
        return rtrim($this->backupPath, '/')
            . '/'
            . ltrim($subPath, '/');
    }

    /**
     * A source argument for scp or rsync: a plain path when the backup is
     * local, otherwise host:path
     */
    public function sourceFor(string $subPath): string
    {
        if ($this->sshHost === null) {
            return $this->pathFor($subPath);
        }

        return $this->sshHost . ':' . $this->pathFor($subPath);
    }
}
