<?php

declare(strict_types=1);

namespace App\Http\Shared;

use App\Shared\Testing\TestCase;
use craft\elements\Entry;
use PHPUnit\Framework\MockObject\MockObject;

use function assert;

trait MockRouteParamsHandlerForTesting
{
    /**
     * @var Entry&MockObject
     * @phpstan-ignore-next-line
     */
    protected mixed $routeParamsHandlerEntry;

    /**
     * @return RouteParamsHandler&MockObject
     */
    protected function mockRouteParamsHandler(): mixed
    {
        assert($this instanceof TestCase);

        $this->routeParamsHandlerEntry = $this->createMock(
            Entry::class,
        );

        $this->routeParamsHandlerEntry->title = 'Test Route Params Entry Title';

        $mock = $this->createMock(RouteParamsHandler::class);

        $mock->method($this::anything())->willReturnCallback(
            function (): Entry {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    object: 'RouteParamsHandler',
                    return: $this->routeParamsHandlerEntry,
                );
            }
        );

        return $mock;
    }
}
