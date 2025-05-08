<?php

declare(strict_types=1);

namespace App\Http\Response\Publications;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Components\Link\Link;
use App\Http\Entities\Meta;
use App\Http\Shared\RouteParamsHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use BuzzingPixel\SlimBridge\ElementSetRoute\RouteParams;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

class DisplayPublicationEntryAction
{
    public function __construct(
        private HeroFactory $heroFactory,
        private RouteParams $routeParams,
        private GenericHandler $fieldHandler,
        private RouteParamsHandler $routeParamsHandler,
        private DisplayPublicationEntryResponder $responder,
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

        $meta = new Meta(metaTitle: (string) $entry->title);

        $breadcrumbs = [
            new Link(
                isEmpty: false,
                content: 'Home',
                href: '/',
            ),
            new Link(
                isEmpty: false,
                content: 'Men of the Mark',
                href: '/publications/men-of-the-mark',
            ),
            new Link(
                isEmpty: false,
                content: 'Viewing Publication',
                href: '',
            ),
        ];

        $hero = $this->heroFactory->createFromDefaults();

        if ($hero->heroHeading() === '') {
            $hero = $hero->withHeroHeading(value: (string) $entry->title);
        }

        return $this->responder->respond(
            $meta,
            $hero,
            $breadcrumbs,
            $this->fieldHandler->getTwigMarkup(
                $entry,
                'body',
            ),
        );
    }
}
