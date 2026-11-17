<?php

declare(strict_types=1);

namespace Cli\Commands\Docker;

use Cli\CliSrcPath;

use function array_merge;
use function file_exists;

readonly class ComposeCommandBuilder
{
    // Git-ignored, machine-specific overrides, such as extra hostnames for
    // one machine's dev sites
    private const string LOCAL_OVERRIDE_FILE = 'docker/docker-compose.local.yml';

    public function __construct(private CliSrcPath $cliSrcPath)
    {
    }

    /** @return string[] */
    public function build(string ...$args): array
    {
        $command = [
            'docker',
            'compose',
            '-f',
            'docker/docker-compose.dev.yml',
        ];

        $localOverridePath = $this->cliSrcPath->pathFromProjectRoot(
            self::LOCAL_OVERRIDE_FILE,
        );

        if (file_exists($localOverridePath)) {
            $command[] = '-f';
            $command[] = self::LOCAL_OVERRIDE_FILE;
        }

        return array_merge(
            $command,
            ['-p', 'stmark'],
            $args,
        );
    }
}
