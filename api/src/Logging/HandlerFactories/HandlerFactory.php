<?php

declare(strict_types=1);

namespace App\Logging\HandlerFactories;

use Monolog\Handler\HandlerInterface;

interface HandlerFactory
{
    public function create(): HandlerInterface;
}
