<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia\Single;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Components\Link\Link;
use App\Http\Entities\Meta;
use App\Http\PageBuilder\Shared\AudioPlayer\AudioPlayerContentModelFactory;
use App\Http\PageBuilder\Shared\AudioPlayer\RenderAudioPlayerFromContentModel;
use App\Http\RouteMiddleware\RequireLogIn\RequireLogInMiddleware;
use App\Http\Shared\RouteParamsHandler;
use BuzzingPixel\SlimBridge\ElementSetRoute\RouteParams;
use BuzzingPixel\SlimBridge\ElementSetRoute\RouteParsing\ParsedRoute;
use BuzzingPixel\SlimBridge\ElementSetRoute\SetRouteFromParsed\RoutingCallbackContract;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouteInterface;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;
use yii\base\InvalidConfigException;

class SingleMessageAction implements RoutingCallbackContract
{
    public static function routingCallback(
        RouteInterface $route,
        ParsedRoute $parsedRoute
    ): void {
        $route->setArgument(
            'pageTitle',
            'Log in to view the members area',
        )
        ->add(RequireLogInMiddleware::class);
    }

    public function __construct(
        private TwigEnvironment $twig,
        private HeroFactory $heroFactory,
        private RouteParams $routeParams,
        private RouteParamsHandler $routeParamsHandler,
        private ResponseFactoryInterface $responseFactory,
        private AudioPlayerContentModelFactory $contentModelFactory,
        private RenderAudioPlayerFromContentModel $renderAudioPlayer,
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
        $mediaEntry = $this->routeParamsHandler->getEntry(
            routeParams: $this->routeParams
        );

        $audioPlayerContentModel = $this->contentModelFactory
            ->makeFromInternalMessageEntry(
                entry: $mediaEntry,
            );

        $audioPlayerMarkup = $this->renderAudioPlayer->render(
            contentModel: $audioPlayerContentModel,
        );

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
                content: 'Internal Media',
                href: '/members/internal-media',
            ),
            new Link(
                isEmpty: false,
                content: 'Viewing Media',
            ),
        ];

        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twig->render(
            '@app/Http/Response/Members/InternalMedia/Single/SingleMessage.twig',
            [
                'breadcrumbs' => $breadcrumbs,
                'meta' => new Meta(
                    metaTitle: $mediaEntry->title . ' | Internal Messages',
                ),
                'hero' => $this->heroFactory->createFromDefaults(),
                'audioPlayerMarkup' => new Markup(
                    $audioPlayerMarkup,
                    'UTF-8',
                ),
            ],
        ));

        return $response;
    }
}
