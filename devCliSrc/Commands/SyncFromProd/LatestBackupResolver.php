<?php

declare(strict_types=1);

namespace Cli\Commands\SyncFromProd;

use RuntimeException;
use Symfony\Component\Process\Process;

use function array_filter;
use function end;
use function implode;
use function is_dir;
use function scandir;
use function str_starts_with;
use function trim;

readonly class LatestBackupResolver
{
    public const string SSH_HOST     = 'Ivy';
    public const string BACKUPS_PATH = '/Volumes/ivy-ext-01/site-backups/backups/stmarkreformed.com';

    public function resolve(): IvyBackup
    {
        // When the backups are on this machine's file system, we're on Ivy,
        // so read them directly instead of going over SSH
        $isLocal = is_dir(self::BACKUPS_PATH);

        $timestamp = $isLocal
            ? $this->latestLocalTimestamp()
            : $this->latestRemoteTimestamp();

        if ($timestamp === '') {
            throw new RuntimeException(
                'No backups found at '
                . self::BACKUPS_PATH
                . ' on '
                . self::SSH_HOST,
            );
        }

        return new IvyBackup(
            sshHost: $isLocal ? null : self::SSH_HOST,
            backupPath: self::BACKUPS_PATH . '/' . $timestamp,
            timestamp: $timestamp,
        );
    }

    private function latestLocalTimestamp(): string
    {
        $entries = scandir(self::BACKUPS_PATH);

        if ($entries === false) {
            throw new RuntimeException(
                'Failed to list backups at ' . self::BACKUPS_PATH,
            );
        }

        // Matches `ls -1 | tail -1`: sorted ascending, hidden entries skipped
        $timestamps = array_filter(
            $entries,
            static fn (string $entry) => ! str_starts_with($entry, '.'),
        );

        return (string) end($timestamps);
    }

    private function latestRemoteTimestamp(): string
    {
        $remoteCommand = implode(' ', [
            '/bin/ls -1',
            self::BACKUPS_PATH,
            '| /usr/bin/tail -1',
        ]);

        $process = new Process(
            command: ['ssh', self::SSH_HOST, $remoteCommand],
            timeout: 60,
        );

        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException(
                'Failed to list backups on '
                . self::SSH_HOST
                . ': '
                . trim($process->getErrorOutput()),
            );
        }

        return trim($process->getOutput());
    }
}
