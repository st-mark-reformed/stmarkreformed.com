<?php

declare(strict_types=1);

namespace Config;

use function restore_error_handler;
use function set_error_handler;
use function str_contains;

use const E_DEPRECATED;

/**
 * Temporary. buzzingpixel/php-queue 2.0.4 and buzzingpixel/php-scheduler
 * 1.1.1 call ReflectionProperty::setAccessible(), which PHP 8.5 deprecates,
 * and dev's Whoops turns that deprecation into an exception. This swallows
 * E_DEPRECATED from those two packages only, and hands everything else to the
 * handler that was registered before it (Whoops, in dev).
 *
 * It has to be registered after Boot::start(), since that's where Whoops
 * registers itself without chaining to an earlier handler.
 *
 * Remove it once both packages ship releases without the call.
 */
readonly class LaggingPackageDeprecationFilter
{
    private const array PACKAGE_PATHS = [
        '/vendor/buzzingpixel/php-queue/',
        '/vendor/buzzingpixel/php-scheduler/',
    ];

    public function register(): void
    {
        // set_error_handler() is the only way to read the current handler
        $previous = set_error_handler(static fn (): bool => false);
        restore_error_handler();

        set_error_handler(static function (
            int $level,
            string $message,
            string $file = '',
            int $line = 0,
        ) use ($previous): bool {
            if ($level === E_DEPRECATED && self::isFromLaggingPackage($file)) {
                return true;
            }

            if ($previous === null) {
                return false;
            }

            return $previous($level, $message, $file, $line) !== false;
        });
    }

    private static function isFromLaggingPackage(string $file): bool
    {
        foreach (self::PACKAGE_PATHS as $path) {
            if (str_contains($file, $path)) {
                return true;
            }
        }

        return false;
    }
}
