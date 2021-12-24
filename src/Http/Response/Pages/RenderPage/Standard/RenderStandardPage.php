<?php

declare(strict_types=1);

namespace App\Http\Response\Pages\RenderPage\Standard;

use App\Http\Components\Hero\Hero;
use App\Http\Entities\Meta;
use App\Http\Response\Pages\RenderPage\RenderPageContract;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;

class RenderStandardPage implements RenderPageContract
{
    public function __construct(
        private Meta $meta,
        private Hero $hero,
        private string $contentString,
        private TwigEnvironment $twig,
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(): string
    {
        return $this->twig->render(
            '@app/Http/Response/Pages/RenderPage/Standard/Page.twig',
            [
                'meta' => $this->meta,
                'hero' => $this->hero,
                'content' => new Markup(
                    $this->contentString,
                    'UTF-8',
                ),
            ],
        );
    }
}
