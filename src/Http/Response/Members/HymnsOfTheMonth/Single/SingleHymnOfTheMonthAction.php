<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth\Single;

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
use yii\base\InvalidConfigException;

class SingleHymnOfTheMonthAction
{
    public function __construct(
        private GetResult $getResult,
        private TwigEnvironment $twig,
        private HeroFactory $heroFactory,
        private RouteParams $routeParams,
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
            routeParams: $this->routeParams,
        );

        $result = $this->getResult->fromEntry(entry: $entry);

        $breadcrumbs = [
            new Link(
                isEmpty: false,
                content: 'Home',
                href: '/',
            ),
            new Link(
                isEmpty: false,
                content: 'Members',
                href: '/members',
            ),
            new Link(
                isEmpty: false,
                content: 'Hymns of the Month',
                href: '/members/hymns-of-the-month',
            ),
            new Link(
                isEmpty: false,
                content: 'Viewing Entry',
            ),
        ];

        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twig->render(
            '@app/Http/Response/Members/HymnsOfTheMonth/Single/SingleHymnOfTheMonth.twig',
            [
                'result' => $result,
                'breadcrumbs' => $breadcrumbs,
                'meta' => new Meta(
                    metaTitle: $entry->title . ' | Hymns of the Month',
                ),
                'hero' => $this->heroFactory->createFromDefaults(),
            ],
        ));

        return $response;
    }
}
