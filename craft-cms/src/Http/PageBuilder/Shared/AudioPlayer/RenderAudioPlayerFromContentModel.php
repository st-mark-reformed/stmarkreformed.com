<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\Shared\AudioPlayer;

use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class RenderAudioPlayerFromContentModel
{
    public function __construct(private TwigEnvironment $twig)
    {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(AudioPlayerContentModel $contentModel): string
    {
        return $this->twig->render(
            '@app/Http/PageBuilder/Shared/AudioPlayer/AudioPlayer.twig',
            ['contentModel' => $contentModel],
        );
    }
}
