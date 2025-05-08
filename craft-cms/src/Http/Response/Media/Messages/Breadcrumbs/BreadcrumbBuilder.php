<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\Breadcrumbs;

use App\Http\Components\Link\Link;
use App\Http\Response\Media\Messages\Params;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;

class BreadcrumbBuilder
{
    public function __construct(private TwigEnvironment $twig)
    {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function fromParams(Params $params): Markup
    {
        if ($params->hasNoSearchParams() && $params->page() < 2) {
            return new Markup('', 'UTF-8');
        }

        $breadcrumbs = [
            new Link(
                isEmpty: false,
                content: 'Home',
                href: '/',
            ),
            new Link(
                isEmpty: false,
                content: 'All Messages',
                href: '/media/messages',
            ),
        ];

        if ($params->hasSearchParams()) {
            $breadcrumbs[] = new Link(
                isEmpty: false,
                content: 'Search',
                href: '/media/messages' . $params->toQueryString(
                    ['page'],
                ),
            );
        }

        if ($params->page() > 1) {
            $breadcrumbs[] = new Link(
                isEmpty: false,
                content: 'Page ' . $params->page(),
                href: '/media/messages' . $params->toQueryString(),
            );
        }

        return new Markup(
            $this->twig->render(
                'Http/_Infrastructure/Breadcrumbs.twig',
                ['breadcrumbs' => $breadcrumbs],
            ),
            'UTF-8',
        );
    }
}
