<?php

declare(strict_types=1);

namespace Cli\Commands\Docker\Up;

use Cli\CliSrcPath;
use Cli\Commands\Docker\Build\BuildCommand;
use Cli\Commands\Docker\Build\BuildIfNeeded;
use Cli\Shared\EnsureDevEnvFiles;
use Cli\StreamCommand;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

use function implode;

readonly class UpCommand
{
    public static function applyCommand(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            implode(' ', [
                'docker:up',
                '[-b|--build]',
                '[-i|--in-place]',
                '[-s|--skip-provision]',
            ]),
            self::class,
        )->descriptions(
            'Brings Docker environment online and runs provisioning as necessary (use --help to see arguments)',
            [
                '--build' => 'Forces a build of the Docker images before running "up"',
                '--in-place' => 'Run docker up without the detach flag',
                '--skip-provision' => 'Skips provisioning scripts',
            ],
        );
    }

    public function __construct(
        private CliSrcPath $cliSrcPath,
        private BuildCommand $buildCommand,
        private BuildIfNeeded $buildIfNeeded,
        private StreamCommand $streamCommand,
        private ConsoleOutputInterface $output,
        private UpPreUpProvision $upPreUpProvision,
        private EnsureDevEnvFiles $ensureDevEnvFiles,
    ) {
    }

    public function __invoke(
        bool $build = false,
        bool $inPlace = false,
        bool $skipProvision = false,
    ): void {
        $this->run(new UpConfig(
            build: $build,
            inPlace: $inPlace,
            skipProvision: $skipProvision,
        ));
    }

    public function run(UpConfig $config = new UpConfig()): void
    {
        $this->ensureDevEnvFiles->run();

        if ($config->build) {
            $this->buildCommand->run();
        } else {
            $this->buildIfNeeded->run();
        }

        if (! $config->skipProvision) {
            $this->upPreUpProvision->run();
        }

        $this->output->writeln(
            '<fg=cyan>Bringing the Docker environment online…</>',
        );

        $cmd = [
            'docker',
            'compose',
            '-f',
            'docker/docker-compose.dev.yml',
            '-p',
            'stmark',
            'up',
        ];

        if ($config->inPlace) {
            $this->streamCommand->stream(
                $cmd,
                $this->cliSrcPath->projectRoot(),
            );

            return;
        }

        $cmd[] = '-d';

        $this->streamCommand->stream(
            $cmd,
            $this->cliSrcPath->projectRoot(),
        );

        $this->output->writeln(
            '<fg=green>Docker environment is online.</>',
        );
    }
}
