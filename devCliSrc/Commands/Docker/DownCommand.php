<?php

declare(strict_types=1);

namespace Cli\Commands\Docker;

use Cli\CliSrcPath;
use Cli\Shared\EnsureDevEnvFiles;
use Cli\StreamCommand;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

readonly class DownCommand
{
    public static function applyCommand(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand('docker:down', self::class)
            ->descriptions('Stops the Docker environment');
    }

    public function __construct(
        private CliSrcPath $cliSrcPath,
        private StreamCommand $streamCommand,
        private ConsoleOutputInterface $output,
        private EnsureDevEnvFiles $ensureDevEnvFiles,
    ) {
    }

    public function __invoke(): void
    {
        $this->run();
    }

    public function run(): void
    {
        $this->ensureDevEnvFiles->run();

        $this->output->writeln(
            '<fg=cyan>Stopping the Docker environment…</>',
        );

        $this->streamCommand->stream(
            [
                'docker',
                'compose',
                '-f',
                'docker/docker-compose.dev.yml',
                '-p',
                'stmark',
                'down',
            ],
            $this->cliSrcPath->projectRoot(),
        );

        $this->output->writeln(
            '<fg=green>Docker environment is offline.</>',
        );
    }
}
