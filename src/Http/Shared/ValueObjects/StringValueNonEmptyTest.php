<?php

declare(strict_types=1);

namespace App\Http\Shared\ValueObjects;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Throwable;

use function assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 * @psalm-suppress MixedArgument
 */
class StringValueNonEmptyTest extends TestCase
{
    public function testEmptyValue(): void
    {
        $exception = null;

        try {
            StringValueNonEmpty::fromString(value: '');
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof InvalidArgumentException);

        self::assertSame(
            'Must not be empty',
            $exception->getMessage(),
        );
    }

    public function test(): void
    {
        $value = StringValueNonEmpty::fromString(value: 'foo');

        self::assertSame('foo', (string) $value);

        self::assertSame('foo', $value->toString());
    }
}
