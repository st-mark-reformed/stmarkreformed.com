<?php

declare(strict_types=1);

namespace Cli\Commands\SyncFromProd;

use Cli\CliSrcPath;
use Cli\StreamCommand;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

use function is_dir;
use function mkdir;
use function rtrim;

readonly class FilesSync
{
    /** @var list<FilesDirectoryMapping> */
    private array $mappings;

    public function __construct(
        private CliSrcPath $cliSrcPath,
        private StreamCommand $streamCommand,
        private ConsoleOutputInterface $output,
    ) {
        $this->mappings = [
            new FilesDirectoryMapping(
                sourceSubPath: 'files',
                localDestRelativeToProjectRoot: 'craft-cms/public/files',
            ),
            new FilesDirectoryMapping(
                sourceSubPath: 'filesAboveWebroot',
                localDestRelativeToProjectRoot: 'craft-cms/filesAboveWebroot',
            ),
            new FilesDirectoryMapping(
                sourceSubPath: 'uploads',
                localDestRelativeToProjectRoot: 'craft-cms/public/uploads',
            ),
            // Only the galleries sub-tree exists in the prod backup
            // (per the `web-public-images-galleries-volume` prod mount),
            // so we sync that sub-tree directly to avoid `--delete`
            // wiping the tracked static assets in `web/public/images/`.
            new FilesDirectoryMapping(
                sourceSubPath: 'images/galleries',
                localDestRelativeToProjectRoot: 'web/public/images/galleries',
            ),
        ];
    }

    public function run(IvyBackup $backup): void
    {
        foreach ($this->mappings as $mapping) {
            $this->syncOne($backup, $mapping);
        }
    }

    private function syncOne(
        IvyBackup $backup,
        FilesDirectoryMapping $mapping,
    ): void {
        $this->output->writeln(
            '<fg=cyan>Syncing '
            . $mapping->sourceSubPath
            . ' → '
            . $mapping->localDestRelativeToProjectRoot
            . '…</>',
        );

        $localDest = $this->cliSrcPath->pathFromProjectRoot(
            $mapping->localDestRelativeToProjectRoot,
        );

        if (! is_dir($localDest)) {
            mkdir($localDest, 0777, true);
        }

        // Trailing slashes ensure rsync mirrors the contents of the
        // source directory into the destination, rather than nesting
        // the source dir inside the destination.
        $this->streamCommand->stream([
            'rsync',
            '-avh',
            '--delete',
            rtrim($backup->sshSourceFor($mapping->sourceSubPath), '/') . '/',
            rtrim($localDest, '/') . '/',
        ]);

        $this->output->writeln(
            '<fg=green>' . $mapping->sourceSubPath . ' synced.</>',
        );
    }
}
