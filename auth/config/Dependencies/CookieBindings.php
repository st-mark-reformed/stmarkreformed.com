<?php

declare(strict_types=1);

namespace Config\Dependencies;

use App\Cookies\Cookies;
use Config\RuntimeConfigOptions;
use Psr\Container\ContainerInterface;
use RxAnte\AppBootstrap\Dependencies\Bindings;
use RxAnte\AppBootstrap\RuntimeConfig;

readonly class CookieBindings
{
    public function __invoke(Bindings $bindings): void
    {
        $bindings->addBinding(
            Cookies::class,
            static function (ContainerInterface $di): Cookies {
                $addedCookies = [];

                $deletedCookies = [];

                return new Cookies(
                    /** @phpstan-ignore-next-line */
                    $_COOKIE,
                    $addedCookies,
                    $deletedCookies,
                    /** @phpstan-ignore-next-line */
                    $di->get(RuntimeConfig::class)->getString(
                        RuntimeConfigOptions::COOKIE_ENCRYPTION_KEY,
                    ),
                );
            },
        );
    }
}
