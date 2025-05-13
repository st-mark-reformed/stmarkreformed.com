<?php

declare(strict_types=1);

namespace Cli\Commands\Docker\Container;

use Cli\CliSrcPath;
use Cli\Shared\DockerImage;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

use function implode;

readonly class ContainerWebNodeCommand
{
    public static function applyCommand(ApplyCliCommandsEvent $commands): void
    {
        ContainerCommand::applyCommand(
            $commands,
            'web-node',
            self::class,
        );
    }

    public function __construct(
        private CliSrcPath $cliSrcPath,
        private ContainerCommand $containerCommand,
    ) {
    }

    /** @param string[] $input */
    public function __invoke(array $input): void
    {
        $containerName = 'stmark_web-node';

        $bHistory = $this->cliSrcPath->dockerPath('web/.bash_history');

        $webDir = $this->cliSrcPath->pathFromProjectRoot('web');

        $this->containerCommand->run(
            container: 'web-node',
            image: DockerImage::web->tag(),
            containerName: $containerName,
            workDir: '/app',
            containerEnv: [
                'NODE_ENV' => 'development',
                'HOSTNAME' => $containerName,
            ],
            mounts: [
                $bHistory => '/root/.bash_history',
                $webDir => '/app',
            ],
            config: new ContainerConfig(
                implode(' ', $input),
            ),
        );
    }
}
