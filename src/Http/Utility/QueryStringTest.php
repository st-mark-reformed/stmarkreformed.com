<?php

declare(strict_types=1);

namespace App\Http\Utility;

use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 * @psalm-suppress MixedArgument
 */
class QueryStringTest extends TestCase
{
    public function testParse(): void
    {
        self::assertSame(
            [
                'foo' => 'bar',
                'baz' => 'foo',
            ],
            QueryString::parse('foo=bar&baz=foo'),
        );
    }

    public function testBuild(): void
    {
        self::assertSame(
            'bar=foo&foo=baz',
            QueryString::build([
                'bar' => 'foo',
                'foo' => 'baz',
            ])
        );
    }
}
