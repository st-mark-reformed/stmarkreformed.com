<?php

declare(strict_types=1);

namespace Cli\Commands\Docker\Container;

use Symfony\Component\Process\Process;

use function array_filter;
use function array_values;
use function explode;
use function implode;
use function stream_isatty;
use function trim;

use const STDIN;

readonly class ContainerConfig
{
    public string $command;

    public function __construct(string $input = '')
    {
        $command = array_values(
            array_filter(
                explode(
                    ' ',
                    $input,
                ),
                static function (string $part): bool {
                    $part = trim($part);

                    return $part !== '';
                },
            ),
        );

        $this->command = implode(' ', $command);
    }

    public function hasCommand(): bool
    {
        return $this->command !== '';
    }

    public function createNotice(string $containerName): string
    {
        return implode(' ', [
            '<fg=yellow>You\'re working inside the \'' . $containerName . '\'',
            'container for this project.</>',
        ]);
    }

    /** @return string[] */
    public function compileExecCommand(string $container): array
    {
        $command = [
            'docker',
            'exec',
        ];

        if ($this->isTtyAvailable()) {
            $command[] = '-it';
        }

        $command[] = 'stmark-' . $container;
        $command[] = 'bash';

        if (! $this->hasCommand()) {
            return $command;
        }

        $command[] = '-c';

        $command[] = $this->command;

        return $command;
    }

    /**
     * @param array<string, string> $containerEnv
     * @param array<string, string> $mounts
     *
     * @return string[]
     */
    public function compileRunCommand(
        string $image,
        string $containerName,
        string $workDir = '',
        array $containerEnv = [],
        array $mounts = [],
    ): array {
        $command = [
            'docker',
            'run',
        ];

        if ($this->isTtyAvailable()) {
            $command[] = '-it';
        }

        $command[] = '--rm';
        $command[] = '--entrypoint';
        $command[] = '';
        $command[] = '--name';
        $command[] = $containerName;

        foreach ($containerEnv as $envKey => $envVal) {
            $command[] = '--env';
            $command[] = $envKey . '=' . $envVal;
        }

        foreach ($mounts as $mountFrom => $mountTo) {
            $command[] = '-v';
            $command[] = $mountFrom . ':' . $mountTo;
        }

        if ($workDir !== '') {
            $command[] = '-w';
            $command[] = $workDir;
        }

        $command[] = $image;

        $command[] = 'bash';

        if (! $this->hasCommand()) {
            return $command;
        }

        $command[] = '-c';

        $command[] = $this->command;

        return $command;
    }

    private function isTtyAvailable(): bool
    {
        return Process::isTtySupported() && stream_isatty(STDIN);
    }
}
