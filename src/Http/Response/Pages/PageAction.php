<?php

declare(strict_types=1);

namespace App\Http\Response\Pages;

use App\Http\Entities\Meta;
use BuzzingPixel\SlimBridge\ElementSetRoute\RouteParams;
use craft\elements\Entry;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

use function assert;

class PageAction
{
    public function __construct(
        private RouteParams $routeParams,
        private ResponseFactoryInterface $responseFactory,
        private TwigEnvironment $twig,
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $entry = $this->routeParams->getParam('element');

        assert($entry instanceof Entry);

        $response = $this->responseFactory->createResponse()
            ->withHeader('EnableStaticCache', 'true');

        $response->getBody()->write($this->twig->render(
            '@app/Http/Response/Pages/Page.twig',
            [
                'meta' => new Meta(),
            ],
        ));

        return $response;
    }
}
