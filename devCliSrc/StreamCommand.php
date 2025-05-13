<?php

declare(strict_types=1);

namespace Cli;

use Symfony\Component\Process\Process;

use function is_string;

readonly class StreamCommand
{
    public function __construct(private CliSrcPath $cliSrcPath)
    {
    }

    /**
     * @param string[]|string $command
     * @param mixed[]|null    $env
     */
    public function stream(
        array|string $command,
        string|null $cwd = null,
        array|null $env = null,
        bool $exitOnError = true,
    ): int {
        if ($cwd === null) {
            $cwd = $this->cliSrcPath->projectRoot();
        }

        if (is_string($command)) {
            $process = Process::fromShellCommandline(
                command: $command,
                cwd: $cwd,
                env: $env,
                timeout: null,
            );
        } else {
            $process = new Process(
                command: $command,
                cwd: $cwd,
                env: $env,
                timeout: null,
            );
        }

        $existStatus = $process->setTty(Process::isTtySupported())
            ->run(static function ($type, $buffer): void {
                echo $buffer;
            });

        if (! $exitOnError || $existStatus === 0) {
            return $existStatus;
        }

        exit($existStatus);
    }
}
