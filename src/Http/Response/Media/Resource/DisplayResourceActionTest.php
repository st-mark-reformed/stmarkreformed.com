<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resource;

use App\Http\Components\Hero\MockHeroFactoryForTesting;
use App\Http\Components\Link\Link;
use App\Http\Shared\MockRouteParamsHandlerForTesting;
use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\MockTwigForTesting;
use App\Shared\Testing\TestCase;
use BuzzingPixel\SlimBridge\ElementSetRoute\RouteParams;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\MockObject\MockObject;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

use function array_map;
use function assert;
use function is_array;

class DisplayResourceActionTest extends TestCase
{
    use MockTwigForTesting;
    use MockHeroFactoryForTesting;
    use MockRouteParamsHandlerForTesting;
    use MockResponseFactoryForTesting;

    private DisplayResourceAction $action;

    private RouteParams $routeParams;

    private ResourceItem $resourceItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->routeParams = new RouteParams();

        $this->resourceItem = $this->createMock(
            ResourceItem::class,
        );

        $this->action = new DisplayResourceAction(
            twig: $this->mockTwig(),
            heroFactory: $this->mockHeroFactory(),
            routeParams: $this->routeParams,
            resourceFactory: $this->mockResourceFactory(),
            routeParamsHandler: $this->mockRouteParamsHandler(),
            responseFactory: $this->mockResponseFactory(),
        );
    }

    /**
     * @return ResourceFactory&MockObject
     */
    private function mockResourceFactory(): ResourceFactory|MockObject
    {
        $factory = $this->createMock(ResourceFactory::class);

        $factory->method('makeFromEntry')->willReturnCallback(
            function (): ResourceItem {
                return $this->genericCall(
                    object: 'ResourceFactory',
                    return: $this->resourceItem,
                );
            }
        );

        return $factory;
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

        self::assertSame(
            'Http/_Infrastructure/Breadcrumbs.twig',
            $call3Args[0],
        );

        $call3Context = $call3Args[1];

        assert(is_array($call3Context));

        self::assertCount(1, $call3Context);

        $call3Breadcrumbs = $call3Context['breadcrumbs'];

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
                    'content' => 'All Resources',
                    'href' => '/resources',
                    'newWindow' => false,
                ],
                [
                    'isEmpty' => false,
                    'content' => 'Viewing Resource',
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
                $call3Breadcrumbs,
            ),
        );

        self::assertSame(
            [
                'object' => 'ResourceFactory',
                'method' => 'makeFromEntry',
                'args' => [$this->routeParamsHandlerEntry],
            ],
            $this->calls[4],
        );
    }
}
