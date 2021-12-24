<?php

declare(strict_types=1);

namespace App\Templating\TwigControl;

use craft\web\twig\TemplateLoader as CraftTemplateLoaderAlias;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigControl
{
    public function __construct(
        private Environment $twig,
        private FilesystemLoader $filesystemLoader,
        private CraftTemplateLoaderAlias $craftTemplateLoader,
    ) {
    }

    public function useCraftTwigLoader(): void
    {
        $this->twig->setLoader($this->craftTemplateLoader);
    }

    public function useCustomTwigLoader(): void
    {
        $this->twig->setLoader($this->filesystemLoader);
    }
}
