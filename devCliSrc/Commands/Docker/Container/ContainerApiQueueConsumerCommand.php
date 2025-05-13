<?php

declare(strict_types=1);

namespace Cli\Commands\Docker\Container;

use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;

use function implode;

readonly class ContainerApiQueueConsumerCommand
{
    public static function applyCommand(ApplyCliCommandsEvent $commands): void
    {
        ContainerCommand::applyCommand(
            $commands,
            'api-queue-consumer',
            self::class,
        );
    }

    public function __construct(private ContainerCommand $containerCommand)
    {
    }

    /** @param string[] $input */
    public function __invoke(array $input): void
    {
        $this->containerCommand->exec(
            'api-queue-consumer',
            new ContainerConfig(
                implode(' ', $input),
            ),
        );
    }
}
