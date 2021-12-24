<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth;

use App\Http\Response\Members\HymnsOfTheMonth\Response\HymnsOfTheMonthResponderContract;
use App\Http\Response\Members\HymnsOfTheMonth\Response\HymnsOfTheMonthResponderFactory;
use App\Http\RouteMiddleware\RequireLogIn\RequireLogInMiddleware;
use App\Shared\Testing\TestCase;
use craft\errors\InvalidFieldException;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteInterface;

class HymnsOfTheMonthActionTest extends TestCase
{
    private HymnResults $results;

    private ResponseInterface $response;

    private HymnsOfTheMonthAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->results = new HymnResults(hasResults: true);

        $this->response = $this->createMock(
            ResponseInterface::class,
        );

        $this->action = new HymnsOfTheMonthAction(
            retrieveHymns: $this->mockRetrieveHymns(),
            responderFactory: $this->mockResponderFactory(),
        );
    }

    private function mockRetrieveHymns(): RetrieveHymns
    {
        $mock = $this->createMock(RetrieveHymns::class);

        $mock->method('retrieve')->willReturn(
            $this->results,
        );

        return $mock;
    }

    private function mockResponderFactory(): HymnsOfTheMonthResponderFactory
    {
        $mock = $this->createMock(
            HymnsOfTheMonthResponderFactory::class,
        );

        $mock->method('make')->willReturnCallback(
            function (): HymnsOfTheMonthResponderContract {
                return $this->genericCall(
                    object: 'HymnsOfTheMonthResponderFactory',
                    return: $this->mockResponder(),
                );
            }
        );

        return $mock;
    }

    private function mockResponder(): HymnsOfTheMonthResponderContract
    {
        $mock = $this->createMock(
            HymnsOfTheMonthResponderContract::class,
        );

        $mock->method('respond')->willReturnCallback(
            function (): ResponseInterface {
                return $this->genericCall(
                    object: 'HymnsOfTheMonthResponderContract',
                    return: $this->response,
                );
            }
        );

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

        HymnsOfTheMonthAction::addRoute(routeCollector: $routeCollector);

        self::assertSame(
            [
                [
                    'object' => 'RouteCollectorProxyInterface',
                    'method' => 'get',
                    'args' => [
                        '/members/hymns-of-the-month',
                        HymnsOfTheMonthAction::class,
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
     * @throws InvalidFieldException
     */
    public function testInvoke(): void
    {
        $response = ($this->action)();

        self::assertSame($this->response, $response);

        self::assertCount(2, $this->calls);

        self::assertSame(
            [
                [
                    'object' => 'HymnsOfTheMonthResponderFactory',
                    'method' => 'make',
                    'args' => [$this->results],
                ],
                [
                    'object' => 'HymnsOfTheMonthResponderContract',
                    'method' => 'respond',
                    'args' => [$this->results],
                ],
            ],
            $this->calls,
        );
    }
}
