<?php

declare(strict_types=1);

namespace App\Http\Components\Hero;

use App\Http\Components\Link\Link;
use App\Http\Components\Link\LinkFactory;
use App\Templating\TwigExtensions\HeroImageUrl\GetDefaultHeroImageUrl;
use craft\elements\Asset;
use craft\elements\db\AssetQuery;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use typedlinkfield\models\Link as LinkFieldModel;
use yii\base\InvalidConfigException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class HeroFactoryTest extends TestCase
{
    /**
     * @return MockObject&LinkFactory
     *
     * @phpstan-ignore-next-line
     */
    private function createLinkFactoryStub(
        LinkFieldModel $expectedInput,
    ): MockObject|LinkFactory {
        $linkFactoryStub = $this->createMock(
            LinkFactory::class,
        );

        $linkFactoryStub->method('fromLinkFieldModel')
            ->with(self::equalTo($expectedInput))
            ->willReturn(new Link(
                isEmpty: false,
                href: 'testHref',
            ));

        return $linkFactoryStub;
    }

    /**
     * @return MockObject&Asset
     *
     * @phpstan-ignore-next-line
     */
    private function createHeroImageAssetStub(): MockObject|Asset
    {
        $assetStub = $this->createMock(Asset::class);

        $assetStub->method('getUrl')->willReturn('testUrl');

        return $assetStub;
    }

    /**
     * @return MockObject&AssetQuery
     *
     * @phpstan-ignore-next-line
     */
    private function createHeroImageQueryStub(
        bool $returnsAsset = false,
    ): AssetQuery|MockObject {
        $assetQueryStub = $this->createMock(
            AssetQuery::class,
        );

        $assetQueryStub->method('one')->willReturn(
            $returnsAsset ? $this->createHeroImageAssetStub() : null,
        );

        return $assetQueryStub;
    }

    /**
     * @return MockObject&Entry
     *
     * @noinspection PhpMixedReturnTypeCanBeReducedInspection
     *
     * @phpstan-ignore-next-line
     */
    private function createEntryStub(
        LinkFieldModel $heroUpperCtaValue,
        AssetQuery|null $heroImageQueryStub = null,
        bool $useShortHeroValue = false,
    ): Entry|MockObject {
        if ($heroImageQueryStub === null) {
            $heroImageQueryStub = $this->createHeroImageQueryStub();
        }

        $entryStub = $this->createMock(Entry::class);

        $entryStub->method('getFieldValue')->willReturnCallback(
            static function (string $fieldHandle) use (
                $heroImageQueryStub,
                $heroUpperCtaValue,
                $useShortHeroValue,
            ): mixed {
                return match ($fieldHandle) {
                    'heroImage' => $heroImageQueryStub,
                    'heroUpperCta' => $heroUpperCtaValue,
                    'heroHeading' => 'testHeroHeadingValue',
                    'heroSubheading' => 'testHeroSubheadingValue',
                    'heroParagraph' => 'testHeroParagraphValue',
                    'useShortHero' => $useShortHeroValue,
                    default => null,
                };
            }
        );

        return $entryStub;
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testCreateFromEntryWhenNoHeroImage(): void
    {
        $linkFieldModel = $this->createMock(
            LinkFieldModel::class,
        );

        $getDefaultHeroImageUrlSpy = $this->createMock(
            GetDefaultHeroImageUrl::class,
        );

        $getDefaultHeroImageUrlSpy->expects(self::never())
            ->method('getDefaultHeroImageUrl');

        $factory = new HeroFactory(
            linkFactory: $this->createLinkFactoryStub(
                expectedInput: $linkFieldModel,
            ),
            getDefaultHeroImageUrl: $getDefaultHeroImageUrlSpy,
        );

        $hero = $factory->createFromEntry(
            $this->createEntryStub(
                heroUpperCtaValue: $linkFieldModel,
                heroImageQueryStub: $this->createHeroImageQueryStub(
                    returnsAsset: true,
                ),
            ),
        );

        self::assertSame(
            'testUrl',
            $hero->heroImageUrl(),
        );

        self::assertSame(
            'testHref',
            $hero->upperCta()->href(),
        );

        self::assertSame(
            'testHeroHeadingValue',
            $hero->heroHeading(),
        );

        self::assertSame(
            'testHeroSubheadingValue',
            $hero->heroSubHeading(),
        );

        self::assertSame(
            'testHeroParagraphValue',
            $hero->heroParagraph(),
        );

        self::assertFalse($hero->useShortHero());
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testCreateFromEntryWhenHeroImage(): void
    {
        $linkFieldModel = $this->createMock(
            LinkFieldModel::class,
        );

        $getDefaultHeroImageUrlSpy = $this->createMock(
            GetDefaultHeroImageUrl::class,
        );

        $getDefaultHeroImageUrlSpy->expects(self::once())
            ->method('getDefaultHeroImageUrl')
            ->willReturn('testDefaultHeroImageUrl');

        $factory = new HeroFactory(
            linkFactory: $this->createLinkFactoryStub(
                expectedInput: $linkFieldModel,
            ),
            getDefaultHeroImageUrl: $getDefaultHeroImageUrlSpy,
        );

        $hero = $factory->createFromEntry(
            $this->createEntryStub(
                heroUpperCtaValue: $linkFieldModel
            ),
        );

        self::assertSame(
            'testDefaultHeroImageUrl',
            $hero->heroImageUrl(),
        );

        self::assertSame(
            'testHref',
            $hero->upperCta()->href(),
        );

        self::assertSame(
            'testHeroHeadingValue',
            $hero->heroHeading(),
        );

        self::assertSame(
            'testHeroSubheadingValue',
            $hero->heroSubHeading(),
        );

        self::assertSame(
            'testHeroParagraphValue',
            $hero->heroParagraph(),
        );

        self::assertFalse($hero->useShortHero());
    }
}
