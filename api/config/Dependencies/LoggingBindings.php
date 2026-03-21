<?php

declare(strict_types=1);

namespace Config\Dependencies;

use App\Logging\LoggerFactory;
use Monolog\Processor\IntrospectionProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use RxAnte\AppBootstrap\Dependencies\Bindings;

use function assert;

readonly class LoggingBindings
{
    public function __invoke(Bindings $bindings): void
    {
        $bindings->addBinding(
            IntrospectionProcessor::class,
            static fn () => new IntrospectionProcessor(),
        );

        $bindings->addBinding(
            LoggerInterface::class,
            static function (ContainerInterface $container): LoggerInterface {
                $loggerFactory = $container->get(LoggerFactory::class);

                assert($loggerFactory instanceof LoggerFactory);

                return $loggerFactory->make();
            },
        );
    }
}
