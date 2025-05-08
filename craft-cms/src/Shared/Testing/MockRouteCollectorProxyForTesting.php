<?php

declare(strict_types=1);

namespace App\Shared\Testing;

use PHPUnit\Framework\MockObject\MockObject;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteInterface;

use function assert;

trait MockRouteCollectorProxyForTesting
{
    /**
     * @return MockObject&RouteCollectorProxyInterface
     */
    public function mockRouteCollector(): mixed
    {
        assert($this instanceof TestCase);

        $mock = $this->createMock(
            RouteCollectorProxyInterface::class,
        );

        $mock->method($this::anything())->willReturnCallback(
            function (): RouteInterface {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    'RouteCollectorProxyInterface',
                    $this->createMock(
                        RouteInterface::class,
                    ),
                );
            }
        );

        return $mock;
    }
}
