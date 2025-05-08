<?php

declare(strict_types=1);

namespace App\Shared\Testing;

use PHPUnit\Framework\MockObject\MockObject;
use Twig\Environment as TwigEnvironment;

use function assert;

trait MockTwigForTesting
{
    /**
     * @return TwigEnvironment&MockObject
     */
    protected function mockTwig(): mixed
    {
        assert($this instanceof TestCase);

        $twig = $this->createMock(TwigEnvironment::class);

        $twig->method($this::anything())->willReturnCallback(
            function (): string {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    object: 'TwigEnvironment',
                    return: 'TwigRender',
                );
            }
        );

        return $twig;
    }
}
