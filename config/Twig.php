<?php

declare(strict_types=1);

namespace Config;

use App\Images\ImageHandler;
use App\Templating\TwigControl\ViewManager;
use App\Templating\TwigExtensions\HeroImage\GetDefaultHeroImageUrl;
use App\Templating\TwigExtensions\HeroImage\GetDefaultHeroOverlayOpacity;
use App\Templating\TwigExtensions\Menu\MenuTwigExtension;
use App\Templating\TwigExtensions\ReadJson\ReadJson;
use App\Templating\TwigExtensions\SlimFlashMessages\SlimFlashMessages;
use BuzzingPixel\TwigMarkdown\MarkdownTwigExtension;
use buzzingpixel\twigsmartypants\SmartypantsTwigExtension;
use buzzingpixel\twigwidont\WidontTwigExtension;
use Psr\Container\ContainerInterface;
use Twig\Loader\FilesystemLoader;

class Twig
{
    public const PATHS = [
        FilesystemLoader::MAIN_NAMESPACE => 'assets/templates',
        'app' => 'src',
    ];

    public const EXTENSIONS = [
        GetDefaultHeroImageUrl::class,
        GetDefaultHeroOverlayOpacity::class,
        MarkdownTwigExtension::class,
        MenuTwigExtension::class,
        ReadJson::class,
        SlimFlashMessages::class,
        SmartypantsTwigExtension::class,
        WidontTwigExtension::class,
    ];

    /**
     * @return mixed[]
     */
    public static function globals(ContainerInterface $di): array
    {
        return [
            'ImageHandler' => $di->get(ImageHandler::class),
            'ViewManager' => $di->get(ViewManager::class),
        ];
    }
}
