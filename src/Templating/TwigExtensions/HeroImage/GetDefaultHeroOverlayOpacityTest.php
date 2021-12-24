<?php

declare(strict_types=1);

namespace App\Templating\TwigExtensions\HeroImage;

use craft\elements\GlobalSet;
use craft\errors\InvalidFieldException;
use craft\services\Globals;
use PHPUnit\Framework\TestCase;

use function assert;
use function is_array;

class GetDefaultHeroOverlayOpacityTest extends TestCase
{
    public function testGetFunctions(): void
    {
        $extension = new GetDefaultHeroOverlayOpacity(
            globals: $this->createMock(Globals::class),
        );

        $functions = $extension->getFunctions();

        self::assertCount(1, $functions);

        self::assertSame(
            'getDefaultHeroOverlayOpacity',
            $functions[0]->getName(),
        );

        $callable = $functions[0]->getCallable();

        assert(is_array($callable));

        self::assertCount(2, $callable);

        self::assertSame(
            $extension,
            $callable[0],
        );

        self::assertSame(
            'getDefaultHeroOverlayOpacity',
            $callable[1],
        );
    }

    /**
     * @throws InvalidFieldException
     */
    public function testGetDefaultHeroImageUrl(): void
    {
        $generalSetStub = $this->createMock(
            GlobalSet::class,
        );

        $generalSetStub->expects(self::once())
            ->method('getFieldValue')
            ->with(self::equalTo(
                'heroDarkeningOverlayOpacity',
            ))
            ->willReturn('123');

        $globalsStub = $this->createMock(Globals::class);

        $globalsStub->expects(self::once())
            ->method('getSetByHandle')
            ->with(self::equalTo('general'))
            ->willReturn($generalSetStub);

        $extension = new GetDefaultHeroOverlayOpacity(globals: $globalsStub);

        self::assertSame(
            123,
            $extension->getDefaultHeroOverlayOpacity(),
        );
    }
}
