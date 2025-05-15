<?php

declare(strict_types=1);

namespace App\Logging;

use App\Logging\HandlerFactories\HandlerFactory;

readonly class HandlerFactoryReference
{
    /** @param class-string<HandlerFactory> $classString */
    public function __construct(public string $classString)
    {
    }
}
