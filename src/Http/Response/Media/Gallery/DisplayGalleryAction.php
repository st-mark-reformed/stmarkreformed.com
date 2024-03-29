<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Gallery;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Components\Link\Link;
use App\Http\Entities\Meta;
use App\Http\Shared\RouteParamsHandler;
use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\FieldHandlers\Matrix\MatrixFieldHandler;
use BuzzingPixel\SlimBridge\ElementSetRoute\RouteParams;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;
use yii\base\InvalidConfigException;

use function array_map;

class DisplayGalleryAction
{
    public function __construct(
        private TwigEnvironment $twig,
        private HeroFactory $heroFactory,
        private RouteParams $routeParams,
        private AssetsFieldHandler $assetsFieldHandler,
        private RouteParamsHandler $routeParamsHandler,
        private MatrixFieldHandler $matrixFieldHandler,
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
                content: 'All Galleries',
                href: '/media/galleries',
            ),
            new Link(
                isEmpty: false,
                content: 'Viewing Gallery',
                href: '',
            ),
        ];

        $videoMatrixItems = $this->matrixFieldHandler->getAll(
            element: $entry,
            field: 'videos',
        );

        $videos = new VideoItems(array_map(
            static function (MatrixBlock $block): VideoItem {
                return new VideoItem(
                    $block->getFieldValue('video'),
                );
            },
            $videoMatrixItems,
        ));

        $items = GalleryItems::fromAssets(
            assets: $this->assetsFieldHandler->getAll(
                element: $entry,
                field: 'gallery',
            ),
        );

        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($this->twig->render(
            '@app/Http/Response/Media/Gallery/DisplayGallery.twig',
            [
                'meta' => new Meta(
                    metaTitle: ((string) $entry->title) . ' | Photo Galleries',
                ),
                'hero' => $this->heroFactory->createFromDefaults(),
                'breadcrumbs' => new Markup(
                    $this->twig->render(
                        'Http/_Infrastructure/Breadcrumbs.twig',
                        ['breadcrumbs' => $breadcrumbs],
                    ),
                    'UTF-8',
                ),
                'videos' => $videos,
                'items' => $items,
            ],
        ));

        return $response;
    }
}
