<?php

declare(strict_types=1);

namespace App\Templating\TwigExtensions\ReadJson;

use PHPUnit\Framework\TestCase;

use function assert;
use function is_array;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class ReadJsonTest extends TestCase
{
    public function testGetFunctions(): void
    {
        $readJson = new ReadJson();

        $functions = $readJson->getFunctions();

        self::assertCount(1, $functions);

        self::assertSame(
            'readJson',
            $functions[0]->getName(),
        );

        $callable = $functions[0]->getCallable();

        assert(is_array($callable));

        self::assertCount(2, $callable);

        self::assertSame(
            $readJson,
            $callable[0],
        );

        self::assertSame(
            'readJsonFunction',
            $callable[1],
        );
    }

    public function testReadJson(): void
    {
        $readJson = new ReadJson();

        $jsonRead = $readJson->readJsonFunction(
            __DIR__ . '/ReadJasonFileTest.json',
        );

        self::assertSame(
            [
                'test' => 'value',
                'foo' => 'bar',
            ],
            $jsonRead,
        );
    }
}
