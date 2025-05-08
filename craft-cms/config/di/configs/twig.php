<?php

declare(strict_types=1);

use Config\Twig;
use craft\web\twig\TemplateLoader;
use craft\web\View;
use Twig\Environment as TwigEnvironment;
use Twig\Loader\FilesystemLoader;

return [
    TemplateLoader::class => static function (): TemplateLoader {
        /** @phpstan-ignore-next-line */
        $loader = Craft::$app->getView()->getTwig()->getLoader();

        assert($loader instanceof TemplateLoader);

        return $loader;
    },
    FilesystemLoader::class => static function (): FilesystemLoader {
        $loader = new FilesystemLoader(
            [],
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
    View::class => static function (): View {
        /** @phpstan-ignore-next-line */
        return Craft::$app->getView();
    },
];
