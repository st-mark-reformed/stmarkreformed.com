<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resource;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Components\Link\Link;
use App\Http\Entities\Meta;
use App\Http\Shared\RouteParamsHandler;
use BuzzingPixel\SlimBridge\ElementSetRoute\RouteParams;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;
use yii\base\InvalidConfigException;

class DisplayResourceAction
{
    public function __construct(
        private TwigEnvironment $twig,
        private HeroFactory $heroFactory,
        private RouteParams $routeParams,
        private ResourceFactory $resourceFactory,
        private RouteParamsHandler $routeParamsHandler,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function __invoke(): ResponseInterface
    {
        $entry = $this->routeParamsHandler->getEntry(
            routeParams: $this->routeParams
        );

        $breadcrumbs = [
            new Link(
                isEmpty: false,
                content: 'Home',
                href: '/',
            ),
            new Link(
                isEmpty: false,
                content: 'All Resources',
                href: '/resources',
            ),
            new Link(
                isEmpty: false,
                content: 'Viewing Resource',
                href: '',
            ),
        ];

        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twig->render(
            '@app/Http/Response/Media/Resource/DisplayResource.twig',
            [
                'meta' => new Meta(
                    metaTitle: ((string) $entry->title) . ' | Resources',
                ),
                'hero' => $this->heroFactory->createFromDefaults(),
                'breadcrumbs' => new Markup(
                    $this->twig->render(
                        'Http/_Infrastructure/Breadcrumbs.twig',
                        ['breadcrumbs' => $breadcrumbs],
                    ),
                    'UTF-8',
                ),
                'resource' => $this->resourceFactory->makeFromEntry(
                    entry: $entry,
                ),
            ],
        ));

        return $response;
    }
}
