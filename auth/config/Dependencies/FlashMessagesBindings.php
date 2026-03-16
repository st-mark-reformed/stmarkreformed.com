<?php

declare(strict_types=1);

namespace Config\Dependencies;

use RxAnte\AppBootstrap\Dependencies\Bindings;
use Slim\Flash\Messages as FlashMessages;

readonly class FlashMessagesBindings
{
    public function __invoke(Bindings $bindings): void
    {
        $bindings->addBinding(
            FlashMessages::class,
            static function () {
                return new FlashMessages($_SESSION);
            },
        );
    }
}
