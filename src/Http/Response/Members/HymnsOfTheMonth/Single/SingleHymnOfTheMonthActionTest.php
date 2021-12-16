<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth\Single;

use App\Http\Components\Hero\MockHeroFactoryForTesting;
use App\Http\Components\Link\Link;
use App\Http\Entities\Meta;
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

class SingleHymnOfTheMonthActionTest extends TestCase
{
    use MockTwigForTesting;
    use MockHeroFactoryForTesting;
    use MockResponseFactoryForTesting;
    use MockRouteParamsHandlerForTesting;

    private RouteParams $routeParams;

    private Result $result;

    private SingleHymnOfTheMonthAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->routeParams = $this->createMock(
            RouteParams::class,
        );

        $this->result = $this->createMock(Result::class);

        $this->action = new SingleHymnOfTheMonthAction(
            twig: $this->mockTwig(),
            routeParams: $this->routeParams,
            getResult: $this->mockGetResult(),
            heroFactory: $this->mockHeroFactory(),
            responseFactory: $this->mockResponseFactory(),
            routeParamsHandler: $this->mockRouteParamsHandler(),
        );
    }

    private function mockGetResult(): GetResult
    {
        $mock = $this->createMock(GetResult::class);

        $mock->method('fromEntry')->willReturnCallback(
            function (): Result {
                return $this->genericCall(
                    object: 'GetResult',
                    return: $this->result,
                );
            }
        );

        return $mock;
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
                'object' => 'GetResult',
                'method' => 'fromEntry',
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

        $args = $this->calls[4]['args'];

        self::assertCount(2, $args);

        self::assertSame(
            '@app/Http/Response/Members/HymnsOfTheMonth/Single/SingleHymnOfTheMonth.twig',
            $args[0],
        );

        $context = $args[1];

        self::assertCount(4, $context);

        self::assertSame($this->result, $context['result']);

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
                    'content' => 'Hymns of the Month',
                    'href' => '/members/hymns-of-the-month',
                    'newWindow' => false,
                ],
                [
                    'isEmpty' => false,
                    'content' => 'Viewing Entry',
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
                $context['breadcrumbs'],
            )
        );

        $meta = $context['meta'];

        assert($meta instanceof Meta);

        self::assertSame(
            'Test Route Params Entry Title | Hymns of the Month',
            $meta->metaTitle(),
        );

        self::assertSame($this->hero, $context['hero']);

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
