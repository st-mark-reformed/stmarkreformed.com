<?php

declare(strict_types=1);

namespace Cli\Commands\SyncFromProd;

use Cli\CliSrcPath;
use Cli\StreamCommand;
use RuntimeException;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

use function escapeshellarg;
use function file_exists;
use function implode;
use function is_dir;
use function mkdir;
use function unlink;

readonly class DatabaseSync
{
    private const string LOCAL_DB_CONTAINER = 'stmark-db';
    private const string DB_ROOT_USER       = 'root';
    private const string DB_ROOT_PASSWORD   = 'root';

    /** @var list<DatabaseDumpMapping> */
    private array $mappings;

    public function __construct(
        private CliSrcPath $cliSrcPath,
        private StreamCommand $streamCommand,
        private ConsoleOutputInterface $output,
    ) {
        $this->mappings = [
            new DatabaseDumpMapping(
                sourceFile: 'stmarkreformed.sql',
                targetDatabase: 'site',
            ),
            new DatabaseDumpMapping(
                sourceFile: 'smrc_api.sql',
                targetDatabase: 'smrc_api',
            ),
            new DatabaseDumpMapping(
                sourceFile: 'smrc_auth.sql',
                targetDatabase: 'smrc_auth',
            ),
        ];
    }

    public function run(IvyBackup $backup): void
    {
        $stagingPath = $this->ensureStagingDir($backup);

        foreach ($this->mappings as $mapping) {
            $this->syncOne($backup, $mapping, $stagingPath);
        }
    }

    private function ensureStagingDir(IvyBackup $backup): string
    {
        $path = $this->cliSrcPath->dockerEphemeralStoragePath(
            'prod-sync/' . $backup->timestamp,
        );

        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }

    private function syncOne(
        IvyBackup $backup,
        DatabaseDumpMapping $mapping,
        string $stagingPath,
    ): void {
        $this->output->writeln(
            '<fg=cyan>Downloading dump for '
            . $mapping->targetDatabase
            . '…</>',
        );

        $localDumpPath = $stagingPath . '/' . $mapping->sourceFile;

        if (file_exists($localDumpPath)) {
            unlink($localDumpPath);
        }

        $this->streamCommand->stream([
            'scp',
            $backup->sshSourceFor($mapping->sourceFile),
            $localDumpPath,
        ]);

        if (! file_exists($localDumpPath)) {
            throw new RuntimeException(
                'Dump file was not downloaded to ' . $localDumpPath,
            );
        }

        $this->output->writeln(
            '<fg=cyan>Resetting '
            . $mapping->targetDatabase
            . ' and importing dump…</>',
        );

        $this->resetDatabase($mapping->targetDatabase);

        $this->importDump(
            mapping: $mapping,
            localDumpPath: $localDumpPath,
        );

        $this->output->writeln(
            '<fg=green>Imported ' . $mapping->targetDatabase . '.</>',
        );
    }

    private function resetDatabase(string $targetDatabase): void
    {
        $sql = implode(' ', [
            'DROP DATABASE IF EXISTS `' . $targetDatabase . '`;',
            'CREATE DATABASE `' . $targetDatabase
                . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;',
        ]);

        $this->streamCommand->stream([
            'docker',
            'exec',
            self::LOCAL_DB_CONTAINER,
            'mysql',
            '-u' . self::DB_ROOT_USER,
            '-p' . self::DB_ROOT_PASSWORD,
            '-e',
            $sql,
        ]);
    }

    private function importDump(
        DatabaseDumpMapping $mapping,
        string $localDumpPath,
    ): void {
        // Pipe the dump file into `docker exec -i mysql` via the shell
        // so MySQL reads the script from stdin in the container.
        $command = implode(' ', [
            'docker exec -i',
            self::LOCAL_DB_CONTAINER,
            'mysql',
            '-u' . self::DB_ROOT_USER,
            '-p' . self::DB_ROOT_PASSWORD,
            $mapping->targetDatabase,
            '<',
            escapeshellarg($localDumpPath),
        ]);

        $this->streamCommand->stream($command);
    }
}
