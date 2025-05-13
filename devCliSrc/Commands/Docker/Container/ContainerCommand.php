<?php

declare(strict_types=1);

namespace Cli\Commands\Docker\Container;

use Cli\StreamCommand;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

readonly class ContainerCommand
{
    public static function applyCommand(
        ApplyCliCommandsEvent $commands,
        string $container,
        string $class,
    ): void {
        $commands->addCommand(
            'docker:container:' . $container . ' [input]*',
            $class,
        )
            ->descriptions(
                'Stops the Docker environment (use --help to see arguments)',
                ['input' => 'If this argument is provided, instead of dropping you into the container shell, the provided command will be run in the container'],
            );
    }

    public function __construct(
        private StreamCommand $streamCommand,
        private ConsoleOutputInterface $output,
    ) {
    }

    public function exec(
        string $container,
        ContainerConfig $config = new ContainerConfig(),
    ): void {
        $this->output->writeln($config->createNotice(
            $container,
        ));

        if (! $config->hasCommand()) {
            $this->output->writeln(
                '<fg=yellow>Remember to exit when you\'re done</>',
            );
        }

        $this->streamCommand->stream(
            $config->compileExecCommand($container),
        );
    }

    /**
     * @param array<string, string> $containerEnv
     * @param array<string, string> $mounts
     */
    public function run(
        string $container,
        string $image,
        string $containerName,
        string $workDir = '',
        array $containerEnv = [],
        array $mounts = [],
        ContainerConfig $config = new ContainerConfig(),
    ): void {
        $this->output->writeln($config->createNotice(
            $container,
        ));

        if (! $config->hasCommand()) {
            $this->output->writeln(
                '<fg=yellow>Remember to exit when you\'re done</>',
            );
        }

        $this->streamCommand->stream(
            $config->compileRunCommand(
                image: $image,
                containerName: $containerName,
                workDir: $workDir,
                containerEnv: $containerEnv,
                mounts: $mounts,
            ),
        );
    }
}
