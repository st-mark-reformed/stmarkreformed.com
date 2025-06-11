<?php

declare(strict_types=1);

namespace Config\Dependencies;

use App\Cli\CliQuestion;
use App\Cli\Question;
use RxAnte\AppBootstrap\Dependencies\Bindings;
use Symfony\Component\Console\Input\ArgvInput;

readonly class RegisterBindingsCli
{
    public static function register(Bindings $bindings): void
    {
        $bindings->addBinding(
            ArgvInput::class,
            static function () {
                return new ArgvInput();
            },
        );

        $bindings->addBinding(
            Question::class,
            $bindings->resolveFromContainer(CliQuestion::class),
        );
    }
}
