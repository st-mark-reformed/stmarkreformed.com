<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth\FileDownload;

use App\Http\RouteMiddleware\RequireLogIn\RequireLogInMiddleware;
use App\Http\Shared\FileDownload\ServeFileDownload;
use App\Shared\Testing\TestCase;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteInterface;
use Throwable;
use yii\base\InvalidConfigException;

use function assert;
use function ucfirst;

class FileDownloadActionTest extends TestCase
{
    private FileDownloadAction $action;

    private bool $hasResult = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hasResult = false;

        $this->action = new FileDownloadAction(
            retrieveResult: $this->mockRetrieveResult(),
            serveFileDownload: $this->mockServeFileDownload(),
        );
    }

    private function mockRetrieveResult(): RetrieveResult
    {
        $mock = $this->createMock(RetrieveResult::class);

        $mock->method('fromAttributes')->willReturnCallback(
            function (): Result {
                return $this->genericCall(
                    object: 'RetrieveResult',
                    return: new Result(
                        hasResult: $this->hasResult,
                        mimeType: 'testMimeType',
                        pathOnServer: '/test/path/on/server',
                    ),
                );
            }
        );

        return $mock;
    }

    /**
     * @return ServeFileDownload&MockObject
     */
    private function mockServeFileDownload(): ServeFileDownload|MockObject
    {
        $mock = $this->createMock(ServeFileDownload::class);

        $mock->method('serve')->willReturnCallback(
            function (): void {
                $this->genericCall(object: 'ServeFileDownload');
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

        $mock->method('getAttribute')->willReturnCallback(
            function (string $name): string {
                return $this->genericCall(
                    object: 'ServerRequestInterface',
                    return: 'test' . ucfirst($name),
                );
            }
        );

        return $mock;
    }

    public function testAddRoute(): void
    {
        $route = $this->createMock(
            RouteInterface::class,
        );

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

        FileDownloadAction::addRoute(routeCollector: $routeCollector);

        self::assertSame(
            [
                [
                    'object' => 'RouteCollectorProxyInterface',
                    'method' => 'get',
                    'args' => [
                        '/members/hymns-of-the-month/{slug}/file/{filePath:.*}',
                        FileDownloadAction::class,
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

    public function testWhenNoResult(): void
    {
        $request = $this->mockRequest();

        $exception = null;

        try {
            ($this->action)(request: $request);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof HttpNotFoundException);

        self::assertSame(
            $request,
            $exception->getRequest(),
        );

        self::assertSame(
            [
                [
                    'object' => 'ServerRequestInterface',
                    'method' => 'getAttribute',
                    'args' => ['slug'],
                ],
                [
                    'object' => 'ServerRequestInterface',
                    'method' => 'getAttribute',
                    'args' => ['filePath'],
                ],
                [
                    'object' => 'RetrieveResult',
                    'method' => 'fromAttributes',
                    'args' => ['testSlug', 'testFilePath'],
                ],
            ],
            $this->calls,
        );
    }

    /**
     * @throws HttpNotFoundException
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testWhenHasResult(): void
    {
        $this->hasResult = true;

        $request = $this->mockRequest();

        ($this->action)(request: $request);

        self::assertSame(
            [
                [
                    'object' => 'ServerRequestInterface',
                    'method' => 'getAttribute',
                    'args' => ['slug'],
                ],
                [
                    'object' => 'ServerRequestInterface',
                    'method' => 'getAttribute',
                    'args' => ['filePath'],
                ],
                [
                    'object' => 'RetrieveResult',
                    'method' => 'fromAttributes',
                    'args' => ['testSlug', 'testFilePath'],
                ],
                [
                    'object' => 'ServeFileDownload',
                    'method' => 'serve',
                    'args' => [
                        $request,
                        '/test/path/on/server',
                        'testMimeType',
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
