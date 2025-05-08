<?php

declare(strict_types=1);

use BuzzingPixel\CraftScheduler\Logging\CraftLogger;
use Psr\Log\LoggerInterface;

use function DI\autowire;

return [
    LoggerInterface::class => autowire(CraftLogger::class),
];
