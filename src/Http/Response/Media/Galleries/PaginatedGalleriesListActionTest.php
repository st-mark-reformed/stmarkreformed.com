<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Galleries;

use App\Http\Pagination\Pagination;
use App\Http\Response\Media\Galleries\Response\PaginatedGalleriesListResponderContract;
use App\Http\Response\Media\Galleries\Response\PaginatedGalleriesListResponderFactory;
use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteInterface;

use function assert;

class PaginatedGalleriesListActionTest extends TestCase
{
    private PaginatedGalleriesListAction $action;

    private GalleryResults $galleryResults;

    private ResponseInterface $response;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new PaginatedGalleriesListAction(
            pageNumberFactory: $this->mockPageNumberFactory(),
            retrieveGalleries: $this->mockRetrieveGalleries(),
            responderFactory: $this->mockResponderFactory(),
        );
    }

    /**
     * @return PageNumberFactory&MockObject
     */
    private function mockPageNumberFactory(): PageNumberFactory|MockObject
    {
        $factory = $this->createMock(
            PageNumberFactory::class,
        );

        $factory->method('fromRequest')->willReturnCallback(
            function (): int {
                return $this->genericCall(
                    object: 'PageNumberFactory',
                    return: 876,
                );
            }
        );

        return $factory;
    }

    /**
     * @return RetrieveGalleries&MockObject
     */
    private function mockRetrieveGalleries(): RetrieveGalleries|MockObject
    {
        $this->galleryResults = new GalleryResults(
            hasEntries: false,
            totalResults: 12,
            incomingItems: [],
        );

        $retrieveGalleries = $this->createMock(
            RetrieveGalleries::class,
        );

        $retrieveGalleries->method('retrieve')->willReturnCallback(
            function (): GalleryResults {
                return $this->genericCall(
                    object: 'RetrieveGalleries',
                    return: $this->galleryResults,
                );
            }
        );

        return $retrieveGalleries;
    }

    /**
     * @return PaginatedGalleriesListResponderFactory&MockObject
     */
    private function mockResponderFactory(): PaginatedGalleriesListResponderFactory|MockObject
    {
        $this->response = $this->createMock(
            ResponseInterface::class,
        );

        $responder = $this->createMock(
            PaginatedGalleriesListResponderContract::class,
        );

        $responder->method('respond')->willReturnCallback(
            function (): ResponseInterface {
                return $this->genericCall(
                    object: 'PaginatedGalleriesListResponderContract',
                    return: $this->response,
                );
            }
        );

        $factory = $this->createMock(
            PaginatedGalleriesListResponderFactory::class,
        );

        $factory->method('make')->willReturnCallback(
            function () use (
                $responder,
            ): PaginatedGalleriesListResponderContract {
                return $this->genericCall(
                    object: 'PaginatedGalleriesListResponderFactory',
                    return: $responder,
                );
            }
        );

        return $factory;
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

        PaginatedGalleriesListAction::addRoute(routeCollector: $routeCollector);

        self::assertSame(
            [
                [
                    'object' => 'RouteCollectorProxyInterface',
                    'method' => 'get',
                    'args' => [
                        '/media/galleries[/page/{pageNum:\d+}]',
                        PaginatedGalleriesListAction::class,
                    ],
                ],
            ],
            $this->calls,
        );
    }

    public function testInvoke(): void
    {
        $request = $this->mockRequest();

        $response = ($this->action)(request: $request);

        self::assertSame(
            $this->response,
            $response,
        );

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
            'RetrieveGalleries',
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
            '/media/galleries',
            $call1Pagination->base(),
        );

        self::assertSame(
            '?foo=bar&baz=foo',
            $call1Pagination->queryString(),
        );

        self::assertSame(
            [
                'object' => 'PaginatedGalleriesListResponderFactory',
                'method' => 'make',
                'args' => [
                    $this->galleryResults,
                    $request,
                ],
            ],
            $this->calls[2],
        );

        self::assertSame(
            'PaginatedGalleriesListResponderContract',
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
            '/media/galleries',
            $call3Pagination->base(),
        );

        self::assertSame(
            '?foo=bar&baz=foo',
            $call3Pagination->queryString(),
        );
    }
}
