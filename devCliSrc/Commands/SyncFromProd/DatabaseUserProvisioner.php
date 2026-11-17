<?php

declare(strict_types=1);

namespace Cli\Commands\SyncFromProd;

use Cli\StreamCommand;
use RuntimeException;
use Symfony\Component\Process\Process;

use function implode;
use function str_replace;
use function trim;

/**
 * Database users live in the database volume, not in the dumps, so a fresh
 * volume has none. Creates the app's user for a database when it's missing,
 * using the credentials the app's container actually connects with.
 */
readonly class DatabaseUserProvisioner
{
    public function __construct(private StreamCommand $streamCommand)
    {
    }

    public function ensureUser(
        DatabaseDumpMapping $mapping,
        string $dbContainer,
        string $rootUser,
        string $rootPassword,
    ): void {
        $user = $this->readContainerEnv(
            $mapping->appContainer,
            $mapping->userEnvVar,
        );

        $password = $this->readContainerEnv(
            $mapping->appContainer,
            $mapping->passwordEnvVar,
        );

        $account = $this->quote($user) . "@'%'";

        $sql = implode(' ', [
            'CREATE USER IF NOT EXISTS ' . $account
                . ' IDENTIFIED BY ' . $this->quote($password) . ';',
            'GRANT ALL PRIVILEGES ON `' . $mapping->targetDatabase . '`.*'
                . ' TO ' . $account . ';',
        ]);

        $this->streamCommand->stream([
            'docker',
            'exec',
            $dbContainer,
            'mysql',
            '-u' . $rootUser,
            '-p' . $rootPassword,
            '-e',
            $sql,
        ]);
    }

    private function readContainerEnv(string $container, string $name): string
    {
        $process = new Process(
            command: ['docker', 'exec', $container, 'printenv', $name],
            timeout: 30,
        );

        $process->run();

        $value = trim($process->getOutput());

        if (! $process->isSuccessful() || $value === '') {
            throw new RuntimeException(
                'Could not read ' . $name . ' from the ' . $container
                . ' container. Is the Docker environment running?',
            );
        }

        return $value;
    }

    /** Quotes a value as a MariaDB string literal */
    private function quote(string $value): string
    {
        return "'" . str_replace(
            ['\\', "'"],
            ['\\\\', "\\'"],
            $value,
        ) . "'";
    }
}
