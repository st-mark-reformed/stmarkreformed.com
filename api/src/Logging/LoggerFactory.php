<?php

declare(strict_types=1);

namespace App\Logging;

use App\Logging\HandlerFactories\HandlerFactory;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use function assert;

readonly class LoggerFactory
{
    public function __construct(
        private ContainerInterface $container,
        private HandlerReferencesFromEnvironment $handlerReferences,
    ) {
    }

    public function make(): LoggerInterface
    {
        $logger = new Logger('app');

        $this->handlerReferences->create()->map(function (
            HandlerFactoryReference $classReference,
        ) use ($logger): void {
            $factory = $this->container->get($classReference->classString);

            assert($factory instanceof HandlerFactory);

            $logger->pushHandler($factory->create());
        });

        return $logger;
    }
}
