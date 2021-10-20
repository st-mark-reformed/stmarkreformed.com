<?php

declare(strict_types=1);

namespace Config;

use App\Templating\TwigExtensions\ReadJson;
use BuzzingPixel\TwigMarkdown\MarkdownTwigExtension;
use buzzingpixel\twigsmartypants\SmartypantsTwigExtension;
use buzzingpixel\twigwidont\WidontTwigExtension;
use Twig\Loader\FilesystemLoader;

class Twig
{
    public const PATHS = [
        FilesystemLoader::MAIN_NAMESPACE => 'assets/templates',
        'app' => 'src',
    ];

    public const EXTENSIONS = [
        MarkdownTwigExtension::class,
        ReadJson::class,
        SmartypantsTwigExtension::class,
        WidontTwigExtension::class,
    ];
}
