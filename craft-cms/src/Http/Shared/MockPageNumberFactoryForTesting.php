<?php

declare(strict_types=1);

namespace App\Http\Shared;

use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

use function assert;

trait MockPageNumberFactoryForTesting
{
    /**
     * @return PageNumberFactory&MockObject
     */
    private function mockPageNumberFactory(): PageNumberFactory|MockObject
    {
        assert($this instanceof TestCase);

        $factory = $this->createMock(
            PageNumberFactory::class,
        );

        $factory->method('fromRequest')->willReturnCallback(
            function (): int {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    object: 'PageNumberFactory',
                    return: 876,
                );
            }
        );

        return $factory;
    }
}
