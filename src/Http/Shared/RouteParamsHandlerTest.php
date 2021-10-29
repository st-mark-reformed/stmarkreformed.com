<?php

declare(strict_types=1);

namespace App\Http\Shared;

use BuzzingPixel\SlimBridge\ElementSetRoute\RouteParams;
use craft\elements\Entry;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class RouteParamsHandlerTest extends TestCase
{
    public function testGetEntry(): void
    {
        $paramEntry = $this->createMock(Entry::class);

        RouteParams::addParam(name: 'element', val: $paramEntry);

        $routeParams = new RouteParams();

        $handler = new RouteParamsHandler();

        self::assertSame(
            $paramEntry,
            $handler->getEntry(routeParams: $routeParams),
        );
    }
}
