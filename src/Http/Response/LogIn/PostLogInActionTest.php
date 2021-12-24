<?php

declare(strict_types=1);

namespace App\Http\Response\LogIn;

use App\Http\Response\LogIn\Response\LogInResponderContract;
use App\Http\Response\LogIn\Response\LogInResponderFactory;
use App\Shared\Testing\MockRouteCollectorProxyForTesting;
use App\Shared\Testing\TestCase;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostLogInActionTest extends TestCase
{
    use MockRouteCollectorProxyForTesting;

    private PostLogInAction $action;

    private ResponseInterface $response;

    private ServerRequestInterface $request;

    private LogInPayload $payload;

    protected function setUp(): void
    {
        parent::setUp();

        $this->response = $this->createMock(
            ResponseInterface::class,
        );

        $this->action = new PostLogInAction(
            craftUserHandler: $this->mockCraftUserHandler(),
            responderFactory: $this->mockResponderFactory(),
        );

        $this->request = $this->createMock(
            ServerRequestInterface::class,
        );

        $this->request->method('getParsedBody')->willReturn([
            'email' => 'foo@bar.baz',
            'password' => 'foo-pass-123',
            'redirect_to' => '/foo/bar/redirect',
        ]);

        $this->payload = new LogInPayload(
            succeeded: true,
            message: 'foo-bar',
        );
    }

    /**
     * @return CraftUserHandler&MockObject
     */
    private function mockCraftUserHandler(): CraftUserHandler|MockObject
    {
        $handler = $this->createMock(
            CraftUserHandler::class,
        );

        $handler->method('logUserIn')->willReturnCallback(
            function (): LogInPayload {
                return $this->genericCall(
                    object: 'CraftUserHandler',
                    return: $this->payload,
                );
            }
        );

        return $handler;
    }

    /**
     * @return LogInResponderFactory&MockObject
     */
    private function mockResponderFactory(): LogInResponderFactory|MockObject
    {
        $responder = $this->createMock(
            LogInResponderContract::class,
        );

        $responder->method('respond')->willReturnCallback(
            function (): ResponseInterface {
                return $this->genericCall(
                    object: 'LogInResponderContract',
                    return: $this->response,
                );
            }
        );

        $factory = $this->createMock(
            LogInResponderFactory::class,
        );

        $factory->method('make')->willReturnCallback(
            function () use ($responder): LogInResponderContract {
                return $this->genericCall(
                    object: 'LogInResponderFactory',
                    return: $responder,
                );
            }
        );

        return $factory;
    }

    public function testAddRoute(): void
    {
        PostLogInAction::addRoute($this->mockRouteCollector());

        self::assertSame(
            [
                [
                    'object' => 'RouteCollectorProxyInterface',
                    'method' => 'post',
                    'args' => [
                        '/log-in',
                        PostLogInAction::class,
                    ],
                ],
            ],
            $this->calls,
        );
    }

    /**
     * @throws Exception
     */
    public function testInvoke(): void
    {
        $response = ($this->action)(request: $this->request);

        self::assertSame($this->response, $response);

        self::assertSame(
            [
                [
                    'object' => 'CraftUserHandler',
                    'method' => 'logUserIn',
                    'args' => [
                        'foo@bar.baz',
                        'foo-pass-123',
                    ],
                ],
                [
                    'object' => 'LogInResponderFactory',
                    'method' => 'make',
                    'args' => [$this->payload],
                ],
                [
                    'object' => 'LogInResponderContract',
                    'method' => 'respond',
                    'args' => [
                        '/foo/bar/redirect',
                        $this->payload,
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
