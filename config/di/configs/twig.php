<?php

declare(strict_types=1);

use Config\Twig;
use Twig\Environment as TwigEnvironment;
use Twig\Loader\FilesystemLoader;

/**
 * @psalm-suppress MixedInferredReturnType
 * @psalm-suppress UndefinedClass
 * @psalm-suppress MixedArgument
 * @psalm-suppress UndefinedConstant
 */
return [
    FilesystemLoader::class => static function (): FilesystemLoader {
        $loader = new FilesystemLoader(
            [],
            /** @phpstan-ignore-next-line */
            CRAFT_BASE_PATH,
        );

        foreach (Twig::PATHS as $nameSpace => $path) {
            $loader->addPath(
                $path,
                $nameSpace,
            );
        }

        return $loader;
    },
    TwigEnvironment::class => static function (): TwigEnvironment {
        /** @phpstan-ignore-next-line */
        return Craft::$app->getView()->getTwig();
    },
];
