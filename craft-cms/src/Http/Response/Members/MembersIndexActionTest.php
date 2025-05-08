<?php

declare(strict_types=1);

namespace App\Http\Response\Members;

use App\Http\RouteMiddleware\RequireLogIn\RequireLogInMiddleware;
use App\Shared\Testing\MockResponseFactoryForTesting;
use App\Shared\Testing\TestCase;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteInterface;

class MembersIndexActionTest extends TestCase
{
    use MockResponseFactoryForTesting;

    private MembersIndexAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new MembersIndexAction(
            responseFactory: $this->mockResponseFactory(),
        );
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

        MembersIndexAction::addRoute(routeCollector: $routeCollector);

        self::assertSame(
            [
                [
                    'object' => 'RouteCollectorProxyInterface',
                    'method' => 'get',
                    'args' => ['/members', MembersIndexAction::class],
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

    public function testInvoke(): void
    {
        $response = ($this->action)();

        self::assertSame($this->response, $response);

        self::assertSame(
            [
                [
                    'object' => 'ResponseFactoryInterface',
                    'method' => 'createResponse',
                    'args' => [303],
                ],
                [
                    'object' => 'ResponseInterface',
                    'method' => 'withHeader',
                    'args' => [
                        'Location',
                        '/members/internal-media',
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
