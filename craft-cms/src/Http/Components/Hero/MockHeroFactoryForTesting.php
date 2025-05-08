<?php

declare(strict_types=1);

namespace App\Http\Components\Hero;

use App\Shared\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

use function assert;

trait MockHeroFactoryForTesting
{
    protected Hero $hero;

    /**
     * @return MockObject&HeroFactory
     */
    protected function mockHeroFactory(): mixed
    {
        assert($this instanceof TestCase);

        $this->hero = $this->createMock(
            Hero::class,
        );

        $twig = $this->createMock(HeroFactory::class);

        $twig->method($this::anything())->willReturnCallback(
            function (): Hero {
                assert($this instanceof TestCase);

                return $this->genericCall(
                    object: 'HeroFactory',
                    return: $this->hero,
                );
            }
        );

        return $twig;
    }
}
