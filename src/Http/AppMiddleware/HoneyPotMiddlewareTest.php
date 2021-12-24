<?php

declare(strict_types=1);

namespace App\Http\AppMiddleware;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpBadRequestException;
use Throwable;

use function assert;

class HoneyPotMiddlewareTest extends TestCase
{
    /** @var MockObject&ServerRequestInterface */
    private mixed $requestStub;

    private string $requestMethod = 'get';

    /** @var array<string, string> */
    private array $requestParsedBody = [];

    /** @var MockObject&ResponseInterface */
    private mixed $responseStub;

    /** @var MockObject&RequestHandlerInterface */
    private mixed $handlerStub;

    /** @var mixed[] */
    private array $handlerCalls = [];

    protected function setUp(): void
    {
        $this->requestParsedBody = [];

        $this->handlerCalls = [];

        parent::setUp();

        $this->requestStub = $this->createMock(
            ServerRequestInterface::class,
        );

        $this->requestStub->method('getMethod')->willReturnCallback(
            function (): string {
                return $this->requestMethod;
            }
        );

        $this->requestStub->method('getParsedBody')
            ->willReturnCallback(
                function (): array {
                    return $this->requestParsedBody;
                }
            );

        $this->responseStub = $this->createMock(
            ResponseInterface::class,
        );

        $this->handlerStub = $this->createMock(
            RequestHandlerInterface::class,
        );

        $this->handlerStub->method('handle')->willReturnCallback(
            function (
                ServerRequestInterface $request,
            ): ResponseInterface {
                $this->handlerCalls[] = [
                    'method' => 'handle',
                    'request' => $request,
                ];

                return $this->responseStub;
            }
        );
    }

    /**
     * @throws HttpBadRequestException
     */
    public function testProcessWhenMethodIsGet(): void
    {
        $response = (new HoneyPotMiddleware())->process(
            request: $this->requestStub,
            handler: $this->handlerStub,
        );

        self::assertSame(
            $this->responseStub,
            $response,
        );

        self::assertCount(1, $this->handlerCalls);

        self::assertSame(
            'handle',
            $this->handlerCalls[0]['method'],
        );

        self::assertSame(
            $this->requestStub,
            $this->handlerCalls[0]['request'],
        );
    }

    public function testProcessWhenAPasswordFieldHasValue(): void
    {
        $this->requestMethod = 'PUT';

        $this->requestParsedBody = [
            'a_password' => 'testVal',
            'fooBaz' => 'bar',
        ];

        $exception = null;

        try {
            (new HoneyPotMiddleware())->process(
                request: $this->requestStub,
                handler: $this->handlerStub,
            );
        } catch (Throwable $e) {
            $exception = $e;
        }

        self::assertCount(0, $this->handlerCalls);

        assert($exception instanceof HttpBadRequestException);

        self::assertSame(
            $this->requestStub,
            $exception->getRequest(),
        );

        self::assertSame(
            'The honeypot field must not be filled in',
            $exception->getMessage(),
        );
    }

    public function testProcessWhenYourCompanyFieldHasValue(): void
    {
        $this->requestMethod = 'PUT';

        $this->requestParsedBody = [
            'a_password' => '',
            'your_company' => 'fooBar',
            'fooBaz' => 'bar',
        ];

        $exception = null;

        try {
            (new HoneyPotMiddleware())->process(
                request: $this->requestStub,
                handler: $this->handlerStub,
            );
        } catch (Throwable $e) {
            $exception = $e;
        }

        self::assertCount(0, $this->handlerCalls);

        assert($exception instanceof HttpBadRequestException);

        self::assertSame(
            $this->requestStub,
            $exception->getRequest(),
        );

        self::assertSame(
            'The honeypot field must not be filled in',
            $exception->getMessage(),
        );
    }

    public function testFinal(): void
    {
        $this->requestMethod = 'POST';

        $this->requestParsedBody = [
            'a_password' => '',
            'your_company' => '',
            'fooBaz' => 'bar',
        ];

        $response = (new HoneyPotMiddleware())->process(
            request: $this->requestStub,
            handler: $this->handlerStub,
        );

        self::assertSame(
            $this->responseStub,
            $response,
        );

        self::assertCount(1, $this->handlerCalls);

        self::assertSame(
            'handle',
            $this->handlerCalls[0]['method'],
        );

        self::assertSame(
            $this->requestStub,
            $this->handlerCalls[0]['request'],
        );
    }
}
