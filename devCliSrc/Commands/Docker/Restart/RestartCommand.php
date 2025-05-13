<?php

declare(strict_types=1);

namespace Cli\Commands\Docker\Restart;

use Cli\Commands\Docker\DownCommand;
use Cli\Commands\Docker\Up\UpCommand;
use Cli\Commands\Docker\Up\UpConfig;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

use function implode;

readonly class RestartCommand
{
    public static function applyCommand(ApplyCliCommandsEvent $commands): void
    {
        $commands->addCommand(
            implode(' ', [
                'docker:restart',
                '[-b|--build]',
                '[-p|--run-provisioning]',
            ]),
            self::class,
        )->descriptions(
            'One command to run down then up (use --help to see arguments)',
            [
                '--build' => 'Forces a build of the Docker images before running "up"',
                '--run-provisioning' => 'Runs provisioning scripts',
            ],
        );
    }

    public function __construct(
        private UpCommand $upCommand,
        private DownCommand $downCommand,
    ) {
    }

    public function __invoke(
        bool $build = false,
        bool $runProvisioning = false,
    ): void {
        $this->run(new RestartConfig(
            build: $build,
            runProvision: $runProvisioning,
        ));
    }

    public function run(RestartConfig $config = new RestartConfig()): void
    {
        $this->downCommand->run();

        $this->upCommand->run(new UpConfig(
            build: $config->build,
            skipProvision: ! $config->runProvision,
        ));
    }
}
