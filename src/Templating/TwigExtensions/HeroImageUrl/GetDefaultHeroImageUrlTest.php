<?php

declare(strict_types=1);

namespace App\Templating\TwigExtensions\HeroImageUrl;

use craft\elements\Asset;
use craft\elements\db\AssetQuery;
use craft\elements\GlobalSet;
use craft\errors\InvalidFieldException;
use craft\services\Globals;
use PHPUnit\Framework\TestCase;
use yii\base\InvalidConfigException;

use function assert;
use function is_array;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class GetDefaultHeroImageUrlTest extends TestCase
{
    public function testGetFunctions(): void
    {
        $extension = new GetDefaultHeroImageUrl(
            globals: $this->createMock(Globals::class),
        );

        $functions = $extension->getFunctions();

        self::assertCount(1, $functions);

        self::assertSame(
            'getDefaultHeroImageUrl',
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
            'getDefaultHeroImageUrl',
            $callable[1],
        );
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testGetDefaultHeroImageUrl(): void
    {
        $asset = $this->createMock(Asset::class);

        $asset->expects(self::once())
            ->method('getUrl')
            ->willReturn('testImageUrl');

        $assetQueryStub = $this->createMock(
            AssetQuery::class,
        );

        $assetQueryStub->expects(self::once())
            ->method('one')
            ->willReturn($asset);

        $generalSetStub = $this->createMock(
            GlobalSet::class,
        );

        $generalSetStub->expects(self::once())
            ->method('getFieldValue')
            ->with(self::equalTo('defaultHeroImage'))
            ->willReturn($assetQueryStub);

        $globalsStub = $this->createMock(Globals::class);

        $globalsStub->expects(self::once())
            ->method('getSetByHandle')
            ->with(self::equalTo('general'))
            ->willReturn($generalSetStub);

        $extension = new GetDefaultHeroImageUrl(globals: $globalsStub);

        self::assertSame(
            'testImageUrl',
            $extension->getDefaultHeroImageUrl(),
        );
    }
}
