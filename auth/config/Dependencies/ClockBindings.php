<?php

declare(strict_types=1);

namespace Config\Dependencies;

use Lcobucci\Clock\SystemClock;
use Psr\Clock\ClockInterface;
use RxAnte\AppBootstrap\Dependencies\Bindings;

readonly class ClockBindings
{
    public function __invoke(Bindings $bindings): void
    {
        $bindings->addBinding(
            ClockInterface::class,
            static function (): ClockInterface {
                return SystemClock::fromUTC();
            },
        );
    }
}
