<?php

declare(strict_types=1);

namespace App\Http\Response\News\NewsItem;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Components\Link\Link;
use App\Http\Entities\Meta;
use App\Http\Shared\RouteParamsHandler;
use BuzzingPixel\SlimBridge\ElementSetRoute\RouteParams;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

class DisplayNewsItemAction
{
    public function __construct(
        private HeroFactory $heroFactory,
        private RouteParams $routeParams,
        private CompileResponse $compileResponse,
        private DisplayNewsItemResponder $responder,
        private RouteParamsHandler $routeParamsHandler,
    ) {
    }

    /**
     * @throws InvalidConfigException
     * @throws InvalidFieldException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(): ResponseInterface
    {
        $entry = $this->routeParamsHandler->getEntry(
            routeParams: $this->routeParams
        );

        $meta = new Meta(metaTitle: (string) $entry->title);

        $breadcrumbs = [
            new Link(
                isEmpty: false,
                content: 'Home',
                href: '/',
            ),
            new Link(
                isEmpty: false,
                content: 'All News',
                href: '/news',
            ),
            new Link(
                isEmpty: false,
                content: 'Viewing News Item',
                href: '',
            ),
        ];

        $hero = $this->heroFactory->createFromEntry(entry: $entry);

        if ($hero->heroHeading() === '') {
            $hero = $hero->withHeroHeading(value: (string) $entry->title);
        }

        return $this->responder->respond(
            meta: $meta,
            hero: $hero,
            breadcrumbs: $breadcrumbs,
            contentString: $this->compileResponse->fromEntry(entry: $entry),
        );
    }
}
