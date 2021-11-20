<?php

declare(strict_types=1);

namespace App\Http\Pagination;

use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;

class RenderPagination
{
    public function __construct(private TwigEnvironment $twig)
    {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(Pagination $pagination): Markup
    {
        return new Markup(
            $this->twig->render(
                '@app/Http/Pagination/Pagination.twig',
                ['pagination' => $pagination],
            ),
            'UTF-8',
        );
    }
}
