<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Gallery;

use App\Http\Components\Hero\MockHeroFactoryForTesting;
use App\Http\Components\Link\Link;
use App\Http\Entities\Meta;
use App\Http\Shared\MockRouteParamsHandlerForTesting;
use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use BuzzingPixel\SlimBridge\ElementSetRoute\RouteParams;
use craft\elements\Asset;
use PHPUnit\Framework\MockObject\MockObject;

use function array_map;
use function assert;

class DisplayGalleryActionTest extends TestCase
{
    use MockTwigForTesting;
    use MockHeroFactoryForTesting;
    use MockRouteParamsHandlerForTesting;
    use MockResponseFactoryForTesting;

    private DisplayGalleryAction $action;

    private RouteParams $routeParams;

    protected function setUp(): void
    {
        parent::setUp();

        $this->routeParams = new RouteParams();

        $this->action = new DisplayGalleryAction(
            twig: $this->mockTwig(),
            heroFactory: $this->mockHeroFactory(),
            routeParams: $this->routeParams,
            assetsFieldHandler: $this->mockAssetsFieldHandler(),
            routeParamsHandler: $this->mockRouteParamsHandler(),
            responseFactory: $this->mockResponseFactory(),
        );
    }

    /**
     * @return AssetsFieldHandler&MockObject
     */
    private function mockAssetsFieldHandler(): AssetsFieldHandler|MockObject
    {
        $assetHandler = $this->createMock(
            AssetsFieldHandler::class,
        );

        $assetHandler->method('getAll')->willReturnCallback(
            function (): array {
                $asset1 = $this->createMock(Asset::class);

                $asset1->title = 'test title 1';

                $asset1->method('getUrl')->willReturn(
                    'test-url-1',
                );

                $asset2 = $this->createMock(Asset::class);

                $asset2->title = 'test title 1';

                $asset2->method('getUrl')->willReturn(
                    'test-url-1',
                );

                return [$asset1, $asset2];
            }
        );

        return $assetHandler;
    }

    public function testInvoke(): void
    {
        $response = ($this->action)();

        self::assertSame($this->response, $response);

        self::assertCount(6, $this->calls);

        self::assertSame(
            [
                'object' => 'RouteParamsHandler',
                'method' => 'getEntry',
                'args' => [$this->routeParams],
            ],
            $this->calls[0],
        );

        self::assertSame(
            [
                'object' => 'ResponseFactoryInterface',
                'method' => 'createResponse',
                'args' => [],
            ],
            $this->calls[1],
        );

        self::assertSame(
            [
                'object' => 'HeroFactory',
                'method' => 'createFromDefaults',
                'args' => [],
            ],
            $this->calls[2],
        );

        self::assertSame(
            'TwigEnvironment',
            $this->calls[3]['object'],
        );

        self::assertSame(
            'render',
            $this->calls[3]['method'],
        );

        $call3Args = $this->calls[3]['args'];

        self::assertCount(2, $call3Args);

        self::assertSame(
            'Http/_Infrastructure/Breadcrumbs.twig',
            $call3Args[0],
        );

        $call3Context = $call3Args[1];

        self::assertCount(1, $call3Context);

        self::assertSame(
            [
                [
                    'isEmpty' => false,
                    'content' => 'Home',
                    'href' => '/',
                    'newWindow' => false,
                ],
                [
                    'isEmpty' => false,
                    'content' => 'All Galleries',
                    'href' => '/media/galleries',
                    'newWindow' => false,
                ],
                [
                    'isEmpty' => false,
                    'content' => 'Viewing Gallery',
                    'href' => '',
                    'newWindow' => false,
                ],
            ],
            array_map(
                static fn (Link $link) => [
                    'isEmpty' => $link->isEmpty(),
                    'content' => $link->content(),
                    'href' => $link->href(),
                    'newWindow' => $link->newWindow(),
                ],
                $call3Context['breadcrumbs'],
            ),
        );

        self::assertSame(
            'TwigEnvironment',
            $this->calls[4]['object'],
        );

        self::assertSame(
            'render',
            $this->calls[4]['method'],
        );

        $call4Args = $this->calls[4]['args'];

        self::assertCount(2, $call4Args);

        self::assertSame(
            '@app/Http/Response/Media/Gallery/DisplayGallery.twig',
            $call4Args[0],
        );

        $call4Context = $call4Args[1];

        self::assertCount(4, $call4Context);

        $meta = $call4Context['meta'];

        assert($meta instanceof Meta);

        self::assertSame(
            'Test Route Params Entry Title | Photo Galleries',
            $meta->metaTitle(),
        );

        self::assertSame(
            $this->hero,
            $call4Context['hero'],
        );

        self::assertSame(
            'TwigRender',
            (string) $call4Context['breadcrumbs'],
        );

        $items = $call4Context['items'];

        assert($items instanceof GalleryItems);

        self::assertSame(
            [
                [
                    'imageUrl' => 'test-url-1',
                    'title' => 'test title 1',
                ],
                [
                    'imageUrl' => 'test-url-1',
                    'title' => 'test title 1',
                ],
            ],
            $items->map(static fn (GalleryItem $item) => [
                'imageUrl' => $item->imgUrl(),
                'title' => $item->title(),
            ]),
        );

        self::assertSame(
            [
                'object' => 'StreamInterface',
                'method' => 'write',
                'args' => [0 => 'TwigRender'],
            ],
            $this->calls[5],
        );
    }
}
