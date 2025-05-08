<?php

declare(strict_types=1);

namespace App\Shared\Utility;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use TS\Text\Truncation;

class TruncateFactoryTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testMake(): void
    {
        $factory = new TruncateFactory();

        $truncate = $factory->make(
            limit: 987,
            strategy: Truncation::STRATEGY_PARAGRAPH,
            truncationString: '___',
            encoding: 'ASCII',
            minLength: 543,
        );

        $ref = new ReflectionClass($truncate);

        $limitProp = $ref->getProperty('max_length');
        $limitProp->setAccessible(true);

        self::assertSame(
            987,
            $limitProp->getValue($truncate),
        );

        $strategyProp = $ref->getProperty('strategy');
        $strategyProp->setAccessible(true);

        self::assertSame(
            'paragraph',
            $strategyProp->getValue($truncate),
        );

        $truncStringProp = $ref->getProperty('truncation_string');
        $truncStringProp->setAccessible(true);

        self::assertSame(
            '___',
            $truncStringProp->getValue($truncate),
        );

        $encodingProp = $ref->getProperty('encoding');
        $encodingProp->setAccessible(true);

        self::assertSame(
            'ASCII',
            $encodingProp->getValue($truncate),
        );

        $minLengthProp = $ref->getProperty('min_length');
        $minLengthProp->setAccessible(true);

        self::assertSame(
            543,
            $minLengthProp->getValue($truncate),
        );
    }
}
