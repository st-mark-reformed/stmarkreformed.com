<?php

declare(strict_types=1);

namespace App\Http\Response\Media\SingleMessage;

use App\Http\Components\Hero\MockHeroFactoryForTesting;
use App\Http\Components\Link\Link;
use App\Http\Entities\Meta;
use App\Http\PageBuilder\Shared\AudioPlayer\MockAudioPlayerContentModelFactoryForTesting;
use App\Http\PageBuilder\Shared\AudioPlayer\MockRenderAudioPlayerFromContentModelForTesting;
use App\Http\Response\Media\Messages\Sidebar\MockMessagesSidebarForTesting;
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
    use MockMessagesSidebarForTesting;
    use MockResponseFactoryForTesting;
    use MockRouteParamsHandlerForTesting;
    use MockAudioPlayerContentModelFactoryForTesting;
    use MockRenderAudioPlayerFromContentModelForTesting;

    private RouteParams $routeParams;

    private SingleMessageAction $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->routeParams = $this->createMock(
            RouteParams::class,
        );

        $this->action = new SingleMessageAction(
            twig: $this->mockTwig(),
            heroFactory: $this->mockHeroFactory(),
            routeParams: $this->routeParams,
            messagesSidebar: $this->mockMessagesSidebar(),
            routeParamsHandler: $this->mockRouteParamsHandler(),
            responseFactory: $this->mockResponseFactory(),
            renderAudioPlayer: $this->mockRenderAudioPlayerFromContentModel(),
            audioPlayerContentModelFactory: $this->mockAudioPlayerContentModelFactory(),
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
        self::assertSame(
            $this->response,
            ($this->action)(),
        );

        self::assertCount(9, $this->calls);

        $call0 = $this->calls[0];

        self::assertSame(
            [
                'object' => 'RouteParamsHandler',
                'method' => 'getEntry',
                'args' => [$this->routeParams],
            ],
            $call0,
        );

        $call1 = $this->calls[1];

        self::assertSame(
            [
                'object' => 'AudioPlayerContentModelFactory',
                'method' => 'makeFromSermonEntry',
                'args' => [$this->routeParamsHandlerEntry],
            ],
            $call1,
        );

        $call2 = $this->calls[2];

        self::assertSame(
            [
                'object' => 'ResponseFactoryInterface',
                'method' => 'createResponse',
                'args' => [],
            ],
            $call2,
        );

        $call3 = $this->calls[3];

        self::assertSame(
            [
                'object' => 'ResponseInterface',
                'method' => 'withHeader',
                'args' => ['EnableStaticCache', 'true'],
            ],
            $call3,
        );

        $call4 = $this->calls[4];

        self::assertSame(
            [
                'object' => 'HeroFactory',
                'method' => 'createFromDefaults',
                'args' => [],
            ],
            $call4,
        );

        $call5 = $this->calls[5];

        self::assertSame(
            [
                'object' => 'MessagesSidebar',
                'method' => 'render',
                'args' => [],
            ],
            $call5,
        );

        $call6 = $this->calls[6];

        self::assertSame('TwigEnvironment', $call6['object']);

        self::assertSame('render', $call6['method']);

        $call6Args = $call6['args'];

        assert(is_array($call6Args));

        self::assertCount(2, $call6Args);

        self::assertSame(
            'Http/_Infrastructure/Breadcrumbs.twig',
            $call6Args[0],
        );

        $call6TwigContext = $call6Args[1];

        assert(is_array($call6TwigContext));

        self::assertCount(1, $call6TwigContext);

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
                    'content' => 'All Messages',
                    'href' => '/media/messages',
                    'newWindow' => false,
                ],
                [
                    'isEmpty' => false,
                    'content' => 'Viewing Message',
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
                $call6TwigContext['breadcrumbs'],
            )
        );

        $call7 = $this->calls[7];

        self::assertSame(
            'TwigEnvironment',
            $call7['object'],
        );

        self::assertSame('render', $call7['method']);

        $call7Args = $call7['args'];

        assert(is_array($call7Args));

        self::assertCount(2, $call7Args);

        self::assertSame(
            '@app/Http/Response/Media/SingleMessage/SingleMessage.twig',
            $call7Args[0],
        );

        $call7TwigContext = $call7Args[1];

        assert(is_array($call7TwigContext));

        self::assertCount(5, $call7TwigContext);

        $meta = $call7TwigContext['meta'];

        assert($meta instanceof Meta);

        self::assertSame(
            'Test Route Params Entry Title | Messages from St. Mark',
            $meta->metaTitle(),
        );

        self::assertSame(
            $this->hero,
            $call7TwigContext['hero'],
        );

        self::assertSame(
            'MessagesSidebarReturn',
            (string) $call7TwigContext['sideBarMarkup'],
        );

        self::assertSame(
            'TwigRender',
            (string) $call7TwigContext['breadcrumbs'],
        );

        self::assertSame(
            'RenderAudioPlayerFromContentModelReturnString',
            (string) $call7TwigContext['audioPlayerMarkup'],
        );
    }
}
