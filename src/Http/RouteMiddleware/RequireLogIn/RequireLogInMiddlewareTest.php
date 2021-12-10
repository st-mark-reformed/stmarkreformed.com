<?php

declare(strict_types=1);

namespace App\Http\RouteMiddleware\RequireLogIn;

use App\Shared\Testing\TestCase;
use craft\errors\InvalidFieldException;
use craft\web\User;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\InvalidConfigException;

class RequireLogInMiddlewareTest extends TestCase
{
    private RequireLogInMiddleware $middleware;

    private bool $isGuest = false;

    private ResponseInterface $responderResponse;

    private ResponseInterface $handlerResponse;

    private ServerRequestInterface $request;

    private string|null $pageTitle = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->isGuest = false;

        $this->responderResponse = $this->createMock(
            ResponseInterface::class,
        );

        $this->handlerResponse = $this->createMock(
            ResponseInterface::class,
        );

        $this->request = $this->mockRequest();

        $this->middleware = new RequireLogInMiddleware(
            user: $this->mockUser(),
            responder: $this->mockResponder(),
        );
    }

    /**
     * @return User&MockObject
     */
    private function mockUser(): User|MockObject
    {
        $user = $this->createMock(User::class);

        $user->method('getIsGuest')->willReturnCallback(
            function (): bool {
                return $this->isGuest;
            }
        );

        return $user;
    }

    /**
     * @return RequireLogInResponder&MockObject
     */
    private function mockResponder(): RequireLogInResponder|MockObject
    {
        $responder = $this->createMock(
            RequireLogInResponder::class,
        );

        $responder->method('respond')->willReturnCallback(
            function (): ResponseInterface {
                return $this->genericCall(
                    object: 'RequireLogInResponder',
                    return: $this->responderResponse,
                );
            }
        );

        return $responder;
    }

    /**
     * @return RequestHandlerInterface&MockObject
     */
    private function mockHandler(): RequestHandlerInterface|MockObject
    {
        $handler = $this->createMock(
            RequestHandlerInterface::class,
        );

        $handler->method('handle')->willReturnCallback(
            function (): ResponseInterface {
                return $this->genericCall(
                    object: 'RequestHandlerInterface',
                    return: $this->handlerResponse,
                );
            }
        );

        return $handler;
    }

    /**
     * @return MockObject&ServerRequestInterface
     */
    private function mockRequest(): ServerRequestInterface|MockObject
    {
        $request = $this->createMock(
            ServerRequestInterface::class,
        );

        $request->method('getAttribute')->willReturnCallback(
            function (): Route {
                return $this->genericCall(
                    object: 'ServerRequestInterface',
                    return: $this->mockRoute(),
                );
            }
        );

        $request->method('getUri')->willReturn(
            $this->mockUri(),
        );

        return $request;
    }

    /**
     * @return Route&MockObject
     */
    private function mockRoute(): Route|MockObject
    {
        $route = $this->createMock(Route::class);

        $route->method('getArgument')->willReturnCallback(
            /** @phpstan-ignore-next-line */
            function (): string|null {
                return $this->genericCall(
                    object: 'Route',
                    return: $this->pageTitle,
                );
            }
        );

        return $route;
    }

    /**
     * @return UriInterface&MockObject
     */
    private function mockUri(): UriInterface|MockObject
    {
        $uri = $this->createMock(UriInterface::class);

        $uri->method('getPath')->willReturn('/test/path');

        return $uri;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testProcessWhenIsGuestAndPageTitleIsNull(): void
    {
        $this->isGuest = true;

        $response = $this->middleware->process(
            request: $this->request,
            handler: $this->mockHandler(),
        );

        self::assertSame(
            $this->responderResponse,
            $response,
        );

        self::assertSame(
            [
                [
                    'object' => 'ServerRequestInterface',
                    'method' => 'getAttribute',
                    'args' => ['__route__'],
                ],
                [
                    'object' => 'Route',
                    'method' => 'getArgument',
                    'args' => ['pageTitle'],
                ],
                [
                    'object' => 'RequireLogInResponder',
                    'method' => 'respond',
                    'args' => [
                        'Log In',
                        '/test/path',
                    ],
                ],
            ],
            $this->calls,
        );
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testProcessWhenIsGuestAndPageTitleIsSet(): void
    {
        $this->isGuest = true;

        $this->pageTitle = 'Test Page Title';

        $response = $this->middleware->process(
            request: $this->request,
            handler: $this->mockHandler(),
        );

        self::assertSame(
            $this->responderResponse,
            $response,
        );

        self::assertSame(
            [
                [
                    'object' => 'ServerRequestInterface',
                    'method' => 'getAttribute',
                    'args' => ['__route__'],
                ],
                [
                    'object' => 'Route',
                    'method' => 'getArgument',
                    'args' => ['pageTitle'],
                ],
                [
                    'object' => 'RequireLogInResponder',
                    'method' => 'respond',
                    'args' => [
                        'Test Page Title',
                        '/test/path',
                    ],
                ],
            ],
            $this->calls,
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws InvalidFieldException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testWhenNotGuest(): void
    {
        $this->isGuest = false;

        $response = $this->middleware->process(
            request: $this->request,
            handler: $this->mockHandler(),
        );

        self::assertSame($this->handlerResponse, $response);

        self::assertSame(
            [
                [
                    'object' => 'RequestHandlerInterface',
                    'method' => 'handle',
                    'args' => [$this->request],
                ],
            ],
            $this->calls,
        );
    }
}
