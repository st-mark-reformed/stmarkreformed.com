<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resources;

use App\Http\Pagination\Pagination;
use App\Http\Response\Media\Resources\Response\PaginatedResourcesResponderContract;
use App\Http\Response\Media\Resources\Response\PaginatedResourcesResponderFactory;
use App\Http\Shared\MockPageNumberFactoryForTesting;
use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteInterface;

use function assert;

class PaginatedResourcesListActionTest extends TestCase
{
    use MockPageNumberFactoryForTesting;

    private PaginatedResourcesListAction $action;

    private ResourceResults $results;

    private ResponseInterface $response;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new PaginatedResourcesListAction(
            pageNumberFactory: $this->mockPageNumberFactory(),
            retrieveResources: $this->mockRetrieveResources(),
            responderFactory: $this->mockResponderFactory(),
        );
    }

    /**
     * @return RetrieveResources&MockObject
     */
    private function mockRetrieveResources(): RetrieveResources|MockObject
    {
        $this->results = new ResourceResults(
            hasEntries: false,
            totalResults: 12,
            incomingItems: [],
        );

        $retrieveResources = $this->createMock(
            RetrieveResources::class,
        );

        $retrieveResources->method('retrieve')->willReturnCallback(
            function (): ResourceResults {
                return $this->genericCall(
                    object: 'RetrieveResources',
                    return: $this->results,
                );
            }
        );

        return $retrieveResources;
    }

    /**
     * @return PaginatedResourcesResponderFactory&MockObject
     */
    private function mockResponderFactory(): PaginatedResourcesResponderFactory|MockObject
    {
        $this->response = $this->createMock(
            ResponseInterface::class,
        );

        $responder = $this->createMock(
            PaginatedResourcesResponderContract::class,
        );

        $responder->method('respond')->willReturnCallback(
            function (): ResponseInterface {
                return $this->genericCall(
                    object: 'PaginatedResourcesResponderContract',
                    return: $this->response,
                );
            }
        );

        $factory = $this->createMock(
            PaginatedResourcesResponderFactory::class,
        );

        $factory->method('make')->willReturnCallback(
            function () use (
                $responder,
            ): PaginatedResourcesResponderContract {
                return $this->genericCall(
                    object: 'PaginatedResourcesResponderFactory',
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

        PaginatedResourcesListAction::addRoute(routeCollector: $routeCollector);

        self::assertSame(
            [
                [
                    'object' => 'RouteCollectorProxyInterface',
                    'method' => 'get',
                    'args' => [
                        '/resources[/page/{pageNum:\d+}]',
                        PaginatedResourcesListAction::class,
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
            'RetrieveResources',
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
            '/resources',
            $call1Pagination->base(),
        );

        self::assertSame(
            '?foo=bar&baz=foo',
            $call1Pagination->queryString(),
        );

        self::assertSame(
            'PaginatedResourcesResponderFactory',
            $this->calls[2]['object'],
        );

        self::assertSame('make', $this->calls[2]['method']);

        $call2Args = $this->calls[2]['args'];

        self::assertCount(3, $call2Args);

        $call2Pagination = $call2Args[0];

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
            '/resources',
            $call2Pagination->base(),
        );

        self::assertSame(
            '?foo=bar&baz=foo',
            $call2Pagination->queryString(),
        );

        self::assertSame($this->results, $call2Args[1]);

        self::assertSame($request, $call2Args[2]);

        self::assertSame(
            'PaginatedResourcesResponderContract',
            $this->calls[3]['object'],
        );

        self::assertSame(
            'respond',
            $this->calls[3]['method'],
        );

        $call3Args = $this->calls[3]['args'];

        self::assertCount(2, $call3Args);

        $call3Pagination = $call3Args[0];

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
            '/resources',
            $call3Pagination->base(),
        );

        self::assertSame(
            '?foo=bar&baz=foo',
            $call3Pagination->queryString(),
        );

        self::assertSame($this->results, $call3Args[1]);
    }
}
