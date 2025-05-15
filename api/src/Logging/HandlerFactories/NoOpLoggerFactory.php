<?php

declare(strict_types=1);

namespace App\Logging\HandlerFactories;

use App\Logging\NoOpLogger;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\PsrHandler;

readonly class NoOpLoggerFactory implements HandlerFactory
{
    public function __construct(private NoOpLogger $logger)
    {
    }

    public function create(): HandlerInterface
    {
        return new PsrHandler($this->logger);
    }
}
