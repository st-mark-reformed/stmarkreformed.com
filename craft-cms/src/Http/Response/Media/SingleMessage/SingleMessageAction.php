<?php

declare(strict_types=1);

namespace App\Http\Response\Media\SingleMessage;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Components\Link\Link;
use App\Http\Entities\Meta;
use App\Http\PageBuilder\Shared\AudioPlayer\AudioPlayerContentModelFactory;
use App\Http\PageBuilder\Shared\AudioPlayer\RenderAudioPlayerFromContentModel;
use App\Http\Response\Media\Messages\Sidebar\MessagesSidebar;
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

class SingleMessageAction
{
    public function __construct(
        private TwigEnvironment $twig,
        private HeroFactory $heroFactory,
        private RouteParams $routeParams,
        private MessagesSidebar $messagesSidebar,
        private RouteParamsHandler $routeParamsHandler,
        private ResponseFactoryInterface $responseFactory,
        private RenderAudioPlayerFromContentModel $renderAudioPlayer,
        private AudioPlayerContentModelFactory $audioPlayerContentModelFactory,
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
        $sermon = $this->routeParamsHandler->getEntry(
            routeParams: $this->routeParams
        );

        $audioPlayerContentModel = $this->audioPlayerContentModelFactory
            ->makeFromSermonEntry(
                sermon: $sermon,
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
                content: 'All Messages',
                href: '/media/messages',
            ),
            new Link(
                isEmpty: false,
                content: 'Viewing Message',
                href: '',
            ),
        ];

        $response = $this->responseFactory->createResponse();

        $response = $response->withHeader(
            'EnableStaticCache',
            'true'
        );

        $response->getBody()->write($this->twig->render(
            '@app/Http/Response/Media/SingleMessage/SingleMessage.twig',
            [
                'meta' => new Meta(
                    metaTitle: $sermon->title . ' | Messages from St. Mark',
                ),
                'hero' => $this->heroFactory->createFromDefaults(),
                'sideBarMarkup' => $this->messagesSidebar->render(),
                'breadcrumbs' => new Markup(
                    $this->twig->render(
                        'Http/_Infrastructure/Breadcrumbs.twig',
                        ['breadcrumbs' => $breadcrumbs],
                    ),
                    'UTF-8',
                ),
                'audioPlayerMarkup' => new Markup(
                    $audioPlayerMarkup,
                    'UTF-8',
                ),
            ],
        ));

        return $response;
    }
}
