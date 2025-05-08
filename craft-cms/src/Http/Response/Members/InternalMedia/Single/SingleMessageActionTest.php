<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia\Single;

use App\Http\Components\Hero\MockHeroFactoryForTesting;
use App\Http\Components\Link\Link;
use App\Http\Entities\Meta;
use App\Http\PageBuilder\Shared\AudioPlayer\MockAudioPlayerContentModelFactoryForTesting;
use App\Http\PageBuilder\Shared\AudioPlayer\MockRenderAudioPlayerFromContentModelForTesting;
use App\Http\Shared\MockRouteParamsHandlerForTesting;
use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use BuzzingPixel\SlimBridge\ElementSetRoute\RouteParams;
use craft\errors\InvalidFieldException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

use function array_map;
use function assert;
use function is_array;

class SingleMessageActionTest extends TestCase
{
    use MockTwigForTesting;
    use MockHeroFactoryForTesting;
    use MockResponseFactoryForTesting;
    use MockRouteParamsHandlerForTesting;
    use MockAudioPlayerContentModelFactoryForTesting;
    use MockRenderAudioPlayerFromContentModelForTesting;

    private RouteParams $routeParams;

    private SingleMessageAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->routeParams = $this->createMock(
            RouteParams::class,
        );

        $this->action = new SingleMessageAction(
            twig: $this->mockTwig(),
            routeParams: $this->routeParams,
            heroFactory: $this->mockHeroFactory(),
            responseFactory: $this->mockResponseFactory(),
            routeParamsHandler: $this->mockRouteParamsHandler(),
            contentModelFactory: $this->mockAudioPlayerContentModelFactory(),
            renderAudioPlayer: $this->mockRenderAudioPlayerFromContentModel(),
        );
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
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
                'object' => 'AudioPlayerContentModelFactory',
                'method' => 'makeFromInternalMessageEntry',
                'args' => [$this->routeParamsHandlerEntry],
            ],
            $this->calls[1],
        );

        self::assertSame(
            [
                'object' => 'ResponseFactoryInterface',
                'method' => 'createResponse',
                'args' => [],
            ],
            $this->calls[2],
        );

        self::assertSame(
            [
                'object' => 'HeroFactory',
                'method' => 'createFromDefaults',
                'args' => [],
            ],
            $this->calls[3],
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

        assert(is_array($call4Args));

        self::assertCount(2, $call4Args);

        self::assertSame(
            '@app/Http/Response/Members/InternalMedia/Single/SingleMessage.twig',
            $call4Args[0],
        );

        $call4Context = $call4Args[1];

        assert(is_array($call4Context));

        self::assertCount(4, $call4Context);

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
                    'content' => 'Members',
                    'href' => '/members',
                    'newWindow' => false,
                ],
                [
                    'isEmpty' => false,
                    'content' => 'Internal Media',
                    'href' => '/members/internal-media',
                    'newWindow' => false,
                ],
                [
                    'isEmpty' => false,
                    'content' => 'Viewing Media',
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
                $call4Context['breadcrumbs'],
            )
        );

        $meta = $call4Context['meta'];

        assert($meta instanceof Meta);

        self::assertSame(
            'Test Route Params Entry Title | Internal Messages',
            $meta->metaTitle(),
        );

        self::assertSame($this->hero, $call4Context['hero']);

        self::assertSame(
            'RenderAudioPlayerFromContentModelReturnString',
            (string) $call4Context['audioPlayerMarkup'],
        );

        self::assertSame(
            [
                'object' => 'StreamInterface',
                'method' => 'write',
                'args' => ['TwigRender'],
            ],
            $this->calls[5],
        );
    }
}
