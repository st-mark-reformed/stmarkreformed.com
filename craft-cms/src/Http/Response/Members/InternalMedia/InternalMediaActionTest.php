<?php

declare(strict_types=1);

namespace App\Http\Response\Members\InternalMedia;

use App\Http\Pagination\Pagination;
use App\Http\Response\Members\InternalMedia\Response\InternalMediaResponderContract;
use App\Http\Response\Members\InternalMedia\Response\InternalMediaResponderFactory;
use App\Http\RouteMiddleware\RequireLogIn\RequireLogInMiddleware;
use App\Http\Shared\MockPageNumberFactoryForTesting;
use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteInterface;

use function assert;

class InternalMediaActionTest extends TestCase
{
    use MockPageNumberFactoryForTesting;

    private ResponseInterface $response;

    private InternalMediaAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->response = $this->createMock(
            ResponseInterface::class,
        );

        $this->action = new InternalMediaAction(
            retrieveMedia: $this->mockRetrieveMedia(),
            responderFactory: $this->mockResponderFactory(),
            pageNumberFactory: $this->mockPageNumberFactory(),
        );
    }

    private function mockRetrieveMedia(): RetrieveMedia
    {
        $mock = $this->createMock(RetrieveMedia::class);

        $mock->method('retrieve')->willReturnCallback(
            function (): MediaResults {
                return $this->genericCall(
                    object: 'RetrieveMedia',
                    return: new MediaResults(
                        hasEntries: true,
                        totalResults: 432,
                        incomingEntries: [],
                    ),
                );
            }
        );

        return $mock;
    }

    private function mockResponderFactory(): InternalMediaResponderFactory
    {
        $mock = $this->createMock(
            InternalMediaResponderFactory::class,
        );

        $mock->method('make')->willReturnCallback(
            function (): InternalMediaResponderContract {
                return $this->genericCall(
                    object: 'InternalMediaResponderFactory',
                    return: $this->mockResponder(),
                );
            }
        );

        return $mock;
    }

    private function mockResponder(): InternalMediaResponderContract
    {
        $mock = $this->createMock(
            InternalMediaResponderContract::class,
        );

        $mock->method('respond')->willReturnCallback(
            function (): ResponseInterface {
                return $this->genericCall(
                    object: 'InternalMediaResponderContract',
                    return: $this->response,
                );
            }
        );

        return $mock;
    }

    /**
     * @return ServerRequestInterface&MockObject
     */
    private function mockRequest(): ServerRequestInterface|MockObject
    {
        $mock = $this->createMock(
            ServerRequestInterface::class,
        );

        $mock->method('getQueryParams')->willReturn([
            'foo' => 'bar',
            'baz' => 'foo',
        ]);

        return $mock;
    }

    public function testAddRoute(): void
    {
        $route = $this->createMock(RouteInterface::class);

        $route->method('setArgument')->willReturnCallback(
            function () use ($route): RouteInterface {
                return $this->genericCall(
                    object: 'RouteInterface',
                    return: $route,
                );
            }
        );

        $route->method('add')->willReturnCallback(
            function () use ($route): RouteInterface {
                return $this->genericCall(
                    object: 'RouteInterface',
                    return: $route,
                );
            }
        );

        $routeCollector = $this->createMock(
            RouteCollectorProxyInterface::class,
        );

        $routeCollector->method(self::anything())
            ->willReturnCallback(
                function () use ($route): RouteInterface {
                    return $this->genericCall(
                        object: 'RouteCollectorProxyInterface',
                        return: $route,
                    );
                }
            );

        InternalMediaAction::addRoute(routeCollector: $routeCollector);

        self::assertSame(
            [
                [
                    'object' => 'RouteCollectorProxyInterface',
                    'method' => 'get',
                    'args' => [
                        '/members/internal-media[/page/{pageNum:\d+}]',
                        InternalMediaAction::class,
                    ],
                ],
                [
                    'object' => 'RouteInterface',
                    'method' => 'setArgument',
                    'args' => [
                        'pageTitle',
                        'Log in to view the members area',
                    ],
                ],
                [
                    'object' => 'RouteInterface',
                    'method' => 'add',
                    'args' => [RequireLogInMiddleware::class],
                ],
            ],
            $this->calls,
        );
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
            'RetrieveMedia',
            $this->calls[1]['object'],
        );

        self::assertSame(
            'retrieve',
            $this->calls[1]['method'],
        );

        self::assertCount(
            1,
            $this->calls[1]['args'],
        );

        $pagination1 = $this->calls[1]['args'][0];

        assert($pagination1 instanceof Pagination);

        self::assertSame(
            '/members/internal-media',
            $pagination1->base(),
        );

        self::assertSame(25, $pagination1->perPage());

        self::assertSame(876, $pagination1->currentPage());

        self::assertSame(
            '?foo=bar&baz=foo',
            $pagination1->queryString(),
        );

        self::assertSame(1, $pagination1->totalResults());

        self::assertSame(
            'InternalMediaResponderFactory',
            $this->calls[2]['object'],
        );

        self::assertSame('make', $this->calls[2]['method']);

        self::assertCount(
            3,
            $this->calls[2]['args'],
        );

        $mediaResults = $this->calls[2]['args'][0];

        assert($mediaResults instanceof MediaResults);

        self::assertSame(432, $mediaResults->totalResults());

        $pagination2 = $this->calls[2]['args'][1];

        assert($pagination2 instanceof Pagination);

        self::assertSame(
            '/members/internal-media',
            $pagination2->base(),
        );

        self::assertSame(25, $pagination2->perPage());

        self::assertSame(876, $pagination2->currentPage());

        self::assertSame(
            '?foo=bar&baz=foo',
            $pagination2->queryString(),
        );

        self::assertSame(432, $pagination2->totalResults());

        self::assertSame(
            $request,
            $this->calls[2]['args'][2],
        );

        self::assertSame(
            'InternalMediaResponderContract',
            $this->calls[3]['object'],
        );

        self::assertSame('respond', $this->calls[3]['method']);

        self::assertCount(
            2,
            $this->calls[3]['args'],
        );

        $mediaResults2 = $this->calls[3]['args'][0];

        self::assertSame($mediaResults, $mediaResults2);

        $pagination3 = $this->calls[3]['args'][1];

        self::assertSame($pagination2, $pagination3);
    }
}
