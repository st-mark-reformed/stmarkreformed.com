<?php

declare(strict_types=1);

namespace App\Http\Response\News\NewsList;

use App\Http\Pagination\Pagination;
use App\Http\Response\News\NewsList\Response\PaginatedNewsListResponderContract;
use App\Http\Response\News\NewsList\Response\PaginatedNewsListResponderFactory;
use App\Http\Shared\MockPageNumberFactoryForTesting;
use App\Shared\Testing\TestCase;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteInterface;
use yii\base\InvalidConfigException;

use function assert;

class PaginatedNewsListActionTest extends TestCase
{
    use MockPageNumberFactoryForTesting;

    private PaginatedNewsListAction $action;

    private NewsResults $results;

    private ResponseInterface $response;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new PaginatedNewsListAction(
            pageNumberFactory: $this->mockPageNumberFactory(),
            retrieveNewsItems: $this->mockRetrieveNewsItems(),
            responderFactory: $this->mockResponderFactory(),
        );
    }

    /**
     * @return RetrieveNewsItems&MockObject
     */
    private function mockRetrieveNewsItems(): RetrieveNewsItems|MockObject
    {
        $this->results = new NewsResults(
            hasEntries: false,
            totalResults: 12,
            incomingItems: [],
        );

        $retrieveNewsItems = $this->createMock(
            RetrieveNewsItems::class
        );

        $retrieveNewsItems->method('retrieve')->willReturnCallback(
            function (): NewsResults {
                return $this->genericCall(
                    object: 'RetrieveNewsItems',
                    return: $this->results,
                );
            }
        );

        return $retrieveNewsItems;
    }

    /**
     * @return PaginatedNewsListResponderFactory&MockObject
     */
    private function mockResponderFactory(): PaginatedNewsListResponderFactory|MockObject
    {
        $this->response = $this->createMock(
            ResponseInterface::class,
        );

        $responder = $this->createMock(
            PaginatedNewsListResponderContract::class,
        );

        $responder->method('respond')->willReturnCallback(
            function (): ResponseInterface {
                return $this->genericCall(
                    object: 'PaginatedNewsListResponderContract',
                    return: $this->response,
                );
            }
        );

        $factory = $this->createMock(
            PaginatedNewsListResponderFactory::class,
        );

        $factory->method('make')->willReturnCallback(
            function () use (
                $responder,
            ): PaginatedNewsListResponderContract {
                return $this->genericCall(
                    object: 'PaginatedNewsListResponderFactory',
                    return: $responder,
                );
            }
        );

        return $factory;
    }

    public function testAddRoute(): void
    {
        $routeCollector = $this->createMock(
            RouteCollectorProxyInterface::class,
        );

        $routeCollector->method(self::anything())
            ->willReturnCallback(
                function (): RouteInterface {
                    return $this->genericCall(
                        object: 'RouteCollectorProxyInterface',
                        return: $this->createMock(
                            RouteInterface::class,
                        ),
                    );
                }
            );

        PaginatedNewsListAction::addRoute(routeCollector: $routeCollector);

        self::assertSame(
            [
                [
                    'object' => 'RouteCollectorProxyInterface',
                    'method' => 'get',
                    'args' => [
                        '/news[/page/{pageNum:\d+}]',
                        PaginatedNewsListAction::class,
                    ],
                ],
            ],
            $this->calls,
        );
    }

    /**
     * @return ServerRequestInterface&MockObject
     */
    private function mockRequest(): ServerRequestInterface|MockObject
    {
        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $request->method('getQueryParams')->willReturn([
            'foo' => 'bar',
            'baz' => 'foo',
        ]);

        return $request;
    }

    /**
     * @throws HttpNotFoundException
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testInvoke(): void
    {
        $request = $this->mockRequest();

        $response = ($this->action)(request: $request);

        self::assertSame($this->response, $response);

        self::assertCount(4, $this->calls);

        self::assertSame(
            [
                'object' => 'PageNumberFactory',
                'method' => 'fromRequest',
                'args' => [$request],
            ],
            $this->calls[0],
        );

        self::assertSame(
            'RetrieveNewsItems',
            $this->calls[1]['object'],
        );

        self::assertSame(
            'retrieve',
            $this->calls[1]['method'],
        );

        $call1Args = $this->calls[1]['args'];

        self::assertCount(1, $call1Args);

        $call1Pagination = $call1Args[0];

        assert($call1Pagination instanceof Pagination);

        self::assertSame(2, $call1Pagination->pad());

        self::assertSame(
            876,
            $call1Pagination->currentPage(),
        );

        self::assertSame(12, $call1Pagination->perPage());

        self::assertSame(
            1,
            $call1Pagination->totalResults(),
        );

        self::assertSame(
            '/news',
            $call1Pagination->base(),
        );

        self::assertSame(
            '?foo=bar&baz=foo',
            $call1Pagination->queryString(),
        );

        self::assertSame(
            'PaginatedNewsListResponderFactory',
            $this->calls[2]['object'],
        );

        self::assertSame('make', $this->calls[2]['method']);

        $call2Args = $this->calls[2]['args'];

        self::assertCount(3, $call2Args);

        self::assertSame($this->results, $call2Args[0]);

        $call2Pagination = $call2Args[1];

        assert($call2Pagination instanceof Pagination);

        self::assertSame(2, $call2Pagination->pad());

        self::assertSame(
            876,
            $call2Pagination->currentPage(),
        );

        self::assertSame(12, $call2Pagination->perPage());

        self::assertSame(
            12,
            $call2Pagination->totalResults(),
        );

        self::assertSame(
            '/news',
            $call2Pagination->base(),
        );

        self::assertSame(
            '?foo=bar&baz=foo',
            $call2Pagination->queryString(),
        );

        self::assertSame($request, $call2Args[2]);

        self::assertSame(
            'PaginatedNewsListResponderContract',
            $this->calls[3]['object'],
        );

        self::assertSame(
            'respond',
            $this->calls[3]['method'],
        );

        $call3Args = $this->calls[3]['args'];

        self::assertCount(2, $call3Args);

        self::assertSame($this->results, $call3Args[0]);

        $call3Pagination = $call3Args[1];

        assert($call3Pagination instanceof Pagination);

        self::assertSame(2, $call3Pagination->pad());

        self::assertSame(
            876,
            $call3Pagination->currentPage(),
        );

        self::assertSame(12, $call3Pagination->perPage());

        self::assertSame(
            12,
            $call3Pagination->totalResults(),
        );

        self::assertSame(
            '/news',
            $call3Pagination->base(),
        );

        self::assertSame(
            '?foo=bar&baz=foo',
            $call3Pagination->queryString(),
        );
    }
}
