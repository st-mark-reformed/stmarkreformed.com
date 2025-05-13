<?php

declare(strict_types=1);

namespace Cli\Commands\Docker\Up;

use Cli\CliSrcPath;
use Cli\Commands\Docker\Build\BuildCommandConfig;
use Cli\Shared\DockerImage;
use Cli\StreamCommand;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

use function file_exists;
use function implode;

readonly class UpPreUpProvision
{
    public function __construct(
        private CliSrcPath $cliSrcPath,
        private StreamCommand $streamCommand,
        private ConsoleOutputInterface $output,
    ) {
    }

    public function run(): void
    {
        new BuildCommandConfig()->walkImages(
            function (DockerImage $image): void {
                $filePathFromDockerDir = $image->getDashCaseName() . '/pre-up-provisioning.sh';

                $provisionFile = $this->cliSrcPath->dockerPath(
                    $filePathFromDockerDir,
                );

                if (! file_exists($provisionFile)) {
                    return;
                }

                $this->output->writeln(
                    implode(' ', [
                        '<fg=cyan>Running pre-up provisioning for',
                        $image->name,
                        '</>',
                    ]),
                );

                $this->streamCommand->stream(
                    [
                        'chmod',
                        '+x',
                        $filePathFromDockerDir,
                    ],
                    $this->cliSrcPath->dockerPath(),
                );

                $this->streamCommand->stream(
                    [
                        'sh',
                        $filePathFromDockerDir,
                    ],
                    $this->cliSrcPath->dockerPath(),
                );

                $this->output->writeln(
                    implode(' ', [
                        '<fg=cyan>Pre-up provisioning for',
                        $image->name,
                        'has finished</>',
                    ]),
                );
            },
        );
    }
}
