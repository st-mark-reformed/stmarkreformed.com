<?php

declare(strict_types=1);

namespace App\Http\Response\News\NewsItem;

use App\Http\Components\Hero\Hero;
use App\Http\Components\Hero\MockHeroFactoryForTesting;
use App\Http\Components\Link\Link;
use App\Http\Entities\Meta;
use App\Http\Shared\MockRouteParamsHandlerForTesting;
use App\Shared\Testing\TestCase;
use BuzzingPixel\SlimBridge\ElementSetRoute\RouteParams;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;

use function array_map;
use function assert;

class DisplayNewsItemActionTest extends TestCase
{
    use MockHeroFactoryForTesting;
    use MockRouteParamsHandlerForTesting;

    private DisplayNewsItemAction $action;

    private RouteParams $routeParams;

    private ResponseInterface $response;

    protected function setUp(): void
    {
        parent::setUp();

        $this->routeParams = new RouteParams();

        $this->response = $this->createMock(
            ResponseInterface::class,
        );

        $this->action = new DisplayNewsItemAction(
            heroFactory: $this->mockHeroFactory(),
            routeParams: $this->routeParams,
            compileResponse: $this->mockCompileResponse(),
            responder: $this->mockResponder(),
            routeParamsHandler: $this->mockRouteParamsHandler(),
        );
    }

    /**
     * @return CompileResponse&MockObject
     */
    private function mockCompileResponse(): CompileResponse|MockObject
    {
        $compiler = $this->createMock(
            CompileResponse::class,
        );

        $compiler->method('fromEntry')->willReturnCallback(
            function (): string {
                return $this->genericCall(
                    object: 'CompileResponse',
                    return: 'CompilerReturn',
                );
            }
        );

        return $compiler;
    }

    /**
     * @return DisplayNewsItemResponder&MockObject
     */
    private function mockResponder(): DisplayNewsItemResponder|MockObject
    {
        $responder = $this->createMock(
            DisplayNewsItemResponder::class,
        );

        $responder->method('respond')->willReturnCallback(
            function (): ResponseInterface {
                return $this->genericCall(
                    object: 'DisplayNewsItemResponder',
                    return: $this->response,
                );
            }
        );

        return $responder;
    }

    public function testInvoke(): void
    {
        $response = ($this->action)();

        self::assertSame($this->response, $response);

        self::assertCount(4, $this->calls);

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
                'object' => 'HeroFactory',
                'method' => 'createFromEntry',
                'args' => [$this->routeParamsHandlerEntry],
            ],
            $this->calls[1],
        );

        self::assertSame(
            [
                'object' => 'CompileResponse',
                'method' => 'fromEntry',
                'args' => [$this->routeParamsHandlerEntry],
            ],
            $this->calls[2],
        );

        self::assertSame(
            [
                'object' => 'DisplayNewsItemResponder',
                'method' => 'respond',
            ],
            [
                'object' => $this->calls[3]['object'],
                'method' => $this->calls[3]['method'],
            ],
        );

        $args = $this->calls[3]['args'];

        self::assertCount(4, $args);

        $meta = $args[0];

        assert($meta instanceof Meta);

        self::assertSame(
            'Test Route Params Entry Title',
            $meta->metaTitle(),
        );

        self::assertInstanceOf(Hero::class, $args[1]);

        $breadcrumbs = $args[2];

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
                    'content' => 'All News',
                    'href' => '/news',
                    'newWindow' => false,
                ],
                [
                    'isEmpty' => false,
                    'content' => 'Viewing News Item',
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
                $breadcrumbs,
            )
        );

        self::assertSame(
            'CompilerReturn',
            $args[3],
        );
    }
}
