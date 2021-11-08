<?php

declare(strict_types=1);

/**
 * @psalm-suppress MissingFile
 * @psalm-suppress MixedArgument
 * @psalm-suppress UnusedFunctionCall
 */
return array_merge(
    require __DIR__ . '/configs/cache.php',
    require __DIR__ . '/configs/craft.php',
    require __DIR__ . '/configs/elastic-search.php',
    require __DIR__ . '/configs/email.php',
    require __DIR__ . '/configs/images.php',
    require __DIR__ . '/configs/logging.php',
    require __DIR__ . '/configs/psr.php',
    require __DIR__ . '/configs/shared.php',
    require __DIR__ . '/configs/static-cache-middleware.php',
    require __DIR__ . '/configs/twig.php',
);
