<?php

declare(strict_types=1);

namespace App\Http\Shared\ValueObjects;

use PHPUnit\Framework\TestCase;

class StringValueTest extends TestCase
{
    public function testStringValueEmpty(): void
    {
        $stringValue = StringValue::fromString(value: '');

        self::assertSame('', (string) $stringValue);

        self::assertSame('', $stringValue->toString());

        self::assertFalse($stringValue->hasValue());

        self::assertTrue($stringValue->hasNoValue());
    }

    public function testStringValueNotEmpty(): void
    {
        $stringValue = StringValue::fromString(value: 'foo');

        self::assertSame('foo', (string) $stringValue);

        self::assertSame('foo', $stringValue->toString());

        self::assertTrue($stringValue->hasValue());

        self::assertFalse($stringValue->hasNoValue());
    }
}
