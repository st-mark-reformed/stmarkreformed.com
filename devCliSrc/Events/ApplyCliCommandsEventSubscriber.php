<?php

declare(strict_types=1);

namespace Cli\Events;
use Cli\Commands\Docker\Build\BuildCommand;
use Cli\Commands\Docker\Container\ContainerApiCommand;
use Cli\Commands\Docker\Container\ContainerApiQueueConsumerCommand;
use Cli\Commands\Docker\Container\ContainerApiScheduleRunnerCommand;
use Cli\Commands\Docker\Container\ContainerAppCommand;
use Cli\Commands\Docker\Container\ContainerDbCommand;
use Cli\Commands\Docker\Container\ContainerWebCommand;
use Cli\Commands\Docker\Container\ContainerWebNodeCommand;
use Cli\Commands\Docker\DownCommand;
use Cli\Commands\Docker\Restart\RestartCommand;
use Cli\Commands\Docker\Up\UpCommand;
use Cli\PhpVersionHandler;
use ReflectionClass;
use RxAnte\AppBootstrap\Cli\ApplyCliCommandsEvent;
use Silly\Application;

use function assert;

readonly class ApplyCliCommandsEventSubscriber
{
    public function onDispatch(ApplyCliCommandsEvent $commands): void
    {
        /**
         * This is a hack because the bootstrap doesn't have an event that
         * we need. We should probably fix that, but for now…
         */
        $reflectionClass = new ReflectionClass($commands);
        $property        = $reflectionClass->getProperty('app');
        /** @noinspection PhpExpressionResultUnusedInspection */
        $property->setAccessible(true);
        $app = $property->getValue($commands);
        assert($app instanceof Application);
        $container      = $app->getContainer();
        $versionHandler = $container->get(PhpVersionHandler::class);
        assert($versionHandler instanceof PhpVersionHandler);
        $versionHandler->warnIfWrongVersion();

        // Docker commands
        BuildCommand::applyCommand($commands);
        RestartCommand::applyCommand($commands);
        UpCommand::applyCommand($commands);
        DownCommand::applyCommand($commands);

        // Docker container commands
        ContainerApiCommand::applyCommand($commands);
        ContainerApiQueueConsumerCommand::applyCommand($commands);
        ContainerApiScheduleRunnerCommand::applyCommand($commands);
        ContainerAppCommand::applyCommand($commands);
        ContainerDbCommand::applyCommand($commands);
        ContainerWebCommand::applyCommand($commands);
        ContainerWebNodeCommand::applyCommand($commands);
    }
}
