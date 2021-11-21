<?php

declare(strict_types=1);

namespace App\Shared\Testing;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

use function assert;

trait MockResponseFactoryForTesting
{
    protected StreamInterface $responseBody;

    protected ResponseInterface $response;

    /**
     * @return MockObject&ResponseFactoryInterface
     */
    protected function mockResponseFactory(): mixed
    {
        assert($this instanceof TestCase);

        $this->responseBody = $this->createMock(
            StreamInterface::class,
        );

        $this->responseBody->method('write')->willReturnCallback(
            function (): int {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    object: 'StreamInterface',
                    return: 123,
                );
            }
        );

        $this->response = $this->createMock(
            ResponseInterface::class,
        );

        $this->response->method('getBody')->willReturn(
            $this->responseBody,
        );

        $this->response->method('withHeader')->willReturnCallback(
            function (): ResponseInterface {
                return $this->genericCall(
                    object: 'ResponseInterface',
                    return: $this->response,
                );
            }
        );

        $mock = $this->createMock(
            ResponseFactoryInterface::class,
        );

        $mock->method($this::anything())->willReturnCallback(
            function (): ResponseInterface {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    object: 'ResponseFactoryInterface',
                    return: $this->response,
                );
            }
        );

        return $mock;
    }
}
