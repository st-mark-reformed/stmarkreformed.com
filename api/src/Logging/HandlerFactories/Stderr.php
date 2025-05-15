<?php

declare(strict_types=1);

namespace App\Logging\HandlerFactories;

use Monolog\Handler\FilterHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Processor\IntrospectionProcessor;

readonly class Stderr implements HandlerFactory
{
    public function __construct(
        private IntrospectionProcessor $introspectionProcessor,
        private string $streamAddress = 'php://stderr',
    ) {
    }

    public function create(): HandlerInterface
    {
        $handler = new StreamHandler($this->streamAddress);

        $handler->pushProcessor($this->introspectionProcessor);

        return new FilterHandler(
            $handler,
            Level::Error,
        );
    }
}
