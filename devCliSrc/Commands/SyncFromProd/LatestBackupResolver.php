<?php

declare(strict_types=1);

namespace Cli\Commands\SyncFromProd;

use RuntimeException;
use Symfony\Component\Process\Process;

use function implode;
use function trim;

readonly class LatestBackupResolver
{
    public const string SSH_HOST         = 'Ivy';
    public const string REMOTE_BASE_PATH = '/Volumes/ivy-ext-01/site-backups/backups/stmarkreformed.com';

    public function resolve(): IvyBackup
    {
        $remoteCommand = implode(' ', [
            '/bin/ls -1',
            self::REMOTE_BASE_PATH,
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

        $timestamp = trim($process->getOutput());

        if ($timestamp === '') {
            throw new RuntimeException(
                'No backups found at '
                . self::REMOTE_BASE_PATH
                . ' on '
                . self::SSH_HOST,
            );
        }

        return new IvyBackup(
            sshHost: self::SSH_HOST,
            remoteBackupPath: self::REMOTE_BASE_PATH . '/' . $timestamp,
            timestamp: $timestamp,
        );
    }
}
