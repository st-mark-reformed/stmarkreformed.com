<?php

declare(strict_types=1);

namespace Config\Dependencies;

use RxAnte\AppBootstrap\Dependencies\Bindings;

class RegisterBindings
{
    public static function register(Bindings $bindings): void
    {
        RegisterBindingsClock::register($bindings);
    }
}
