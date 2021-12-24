<?php

declare(strict_types=1);

namespace App\Http\Response\News\NewsItem;

use App\Http\Components\Hero\Hero;
use App\Http\Components\Link\Link;
use App\Http\Entities\Meta;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;

class DisplayNewsItemResponder
{
    public function __construct(
        private TwigEnvironment $twig,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    /**
     * @param Link[] $breadcrumbs
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function respond(
        Meta $meta,
        Hero $hero,
        array $breadcrumbs,
        string $contentString,
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse();

        $response = $response->withHeader(
            'EnableStaticCache',
            'true'
        );

        $response->getBody()->write($this->twig->render(
            '@app/Http/Response/Pages/RenderPage/Standard/Page.twig',
            [
                'meta' => $meta,
                'hero' => $hero,
                'breadcrumbs' => new Markup(
                    $this->twig->render(
                        'Http/_Infrastructure/Breadcrumbs.twig',
                        ['breadcrumbs' => $breadcrumbs],
                    ),
                    'UTF-8',
                ),
                'content' => new Markup(
                    $contentString,
                    'UTF-8',
                ),
            ]
        ));

        return $response;
    }
}
