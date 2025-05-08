<?php

declare(strict_types=1);

namespace App\Http\Components\Hero;

use App\Http\Components\Link\Link;
use App\Http\Components\Link\LinkFactory;
use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\LinkField\LinkFieldHandler;
use App\Templating\TwigExtensions\HeroImage\GetDefaultHeroImageUrl;
use App\Templating\TwigExtensions\HeroImage\GetDefaultHeroOverlayOpacity;
use craft\base\Element;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use typedlinkfield\models\Link as LinkFieldModel;
use yii\base\InvalidConfigException;

use function array_pop;
use function array_values;
use function assert;

class HeroFactoryTest extends TestCase
{
    private HeroFactory $factory;

    /**
     * @var Entry&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $entry;

    /** @var mixed[] */
    private array $calls;

    private bool $assetHandlerReturnsAsset = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->assetHandlerReturnsAsset = false;

        $this->entry = $this->createMock(Entry::class);

        $this->factory = new HeroFactory(
            linkFactory: $this->mockLinkFactory(),
            genericHandler: $this->mockGenericHandler(),
            linkFieldHandler: $this->mockLinkFieldHandler(),
            assetsFieldHandler: $this->mockAssetsFieldHandler(),
            defaultImageUrl: $this->mockGetDefaultHeroImageUrl(),
            defaultOverlayOpacity: $this->mockGetDefaultHeroOverlayOpacity(),
        );
    }

    /**
     * @return MockObject&LinkFactory
     */
    private function mockLinkFactory(): mixed
    {
        $linkFactory = $this->createMock(LinkFactory::class);

        $linkFactory->method('fromLinkFieldModel')
            ->willReturnCallback(
                function (LinkFieldModel $linkFieldModel): Link {
                    $this->calls[] = [
                        'object' => 'LinkFactory',
                        'method' => 'fromLinkFieldModel',
                        'linkFieldModel' => $linkFieldModel,
                    ];

                    return new Link(
                        isEmpty: false,
                        content: 'testLinkContent',
                        href: 'testLinkHref',
                        newWindow: false,
                    );
                }
            );

        return $linkFactory;
    }

    /**
     * @return MockObject&GenericHandler
     */
    private function mockGenericHandler(): mixed
    {
        $handler = $this->createMock(GenericHandler::class);

        $handler->method('getString')
            ->willReturnCallback(
                function (Element $element, string $field): string {
                    $this->calls[] = [
                        'object' => 'GenericHandler',
                        'method' => 'getString',
                        'element' => $element,
                        'field' => $field,
                    ];

                    return 'testGetString';
                }
            );

        $handler->method('getBoolean')
            ->willReturnCallback(
                function (Element $element, string $field): bool {
                    $this->calls[] = [
                        'object' => 'GenericHandler',
                        'method' => 'getBoolean',
                        'element' => $element,
                        'field' => $field,
                    ];

                    return true;
                }
            );

        $handler->method('getInt')
            ->willReturnCallback(
                function (Element $element, string $field): int {
                    $this->calls[] = [
                        'object' => 'GenericHandler',
                        'method' => 'getBoolean',
                        'element' => $element,
                        'field' => $field,
                    ];

                    return 587;
                }
            );

        return $handler;
    }

    /**
     * @return MockObject&LinkFieldHandler
     */
    private function mockLinkFieldHandler(): mixed
    {
        $handler = $this->createMock(
            LinkFieldHandler::class,
        );

        $handler->method('getModel')->willReturnCallback(
            function (Element $element, string $field): LinkFieldModel {
                $this->calls[] = [
                    'object' => 'LinkFieldHandler',
                    'method' => 'getModel',
                    'element' => $element,
                    'field' => $field,
                ];

                $model = $this->createMock(
                    LinkFieldModel::class,
                );

                $model->method('getLink')->willReturn(
                    'testLink',
                );

                return $model;
            }
        );

        return $handler;
    }

    /**
     * @return MockObject&AssetsFieldHandler
     */
    private function mockAssetsFieldHandler(): mixed
    {
        $handler = $this->createMock(
            AssetsFieldHandler::class,
        );

        $handler->method('getOneOrNull')->willReturnCallback(
            function (Element $element, string $field): ?Asset {
                $this->calls[] = [
                    'object' => 'AssetsFieldHandler',
                    'method' => 'getOneOrNull',
                    'element' => $element,
                    'field' => $field,
                ];

                if (! $this->assetHandlerReturnsAsset) {
                    return null;
                }

                $model = $this->createMock(Asset::class);

                $model->method('getUrl')->willReturn(
                    'testAssetUrl',
                );

                return $model;
            }
        );

        return $handler;
    }

    /**
     * @return MockObject&GetDefaultHeroImageUrl
     */
    private function mockGetDefaultHeroImageUrl(): mixed
    {
        $get = $this->createMock(
            GetDefaultHeroImageUrl::class,
        );

        $get->method('getDefaultHeroImageUrl')->willReturnCallback(
            function (): string {
                $this->calls[] = [
                    'object' => 'GetDefaultHeroImageUrl',
                    'method' => 'getDefaultHeroImageUrl',
                ];

                return 'testDefaultHeroImageUrl';
            }
        );

        return $get;
    }

    /**
     * @return MockObject&GetDefaultHeroOverlayOpacity
     */
    private function mockGetDefaultHeroOverlayOpacity(): mixed
    {
        $get = $this->createMock(
            GetDefaultHeroOverlayOpacity::class,
        );

        $get->method('getDefaultHeroOverlayOpacity')
            ->willReturnCallback(function (): int {
                $this->calls[] = [
                    'object' => 'GetDefaultHeroOverlayOpacity',
                    'method' => 'getDefaultHeroOverlayOpacity',
                ];

                return 476;
            });

        return $get;
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testCreateFromEntryWhenNoHeroImage(): void
    {
        $hero = $this->factory->createFromEntry(entry: $this->entry);

        self::assertSame(476, $hero->heroOverlayOpacity());

        self::assertSame(
            'testDefaultHeroImageUrl',
            $hero->heroImageUrl(),
        );

        self::assertSame(
            'testLinkContent',
            $hero->upperCta()->content(),
        );

        self::assertSame(
            'testLinkHref',
            $hero->upperCta()->href(),
        );

        self::assertSame(
            'testGetString',
            $hero->heroHeading(),
        );

        self::assertSame(
            'testGetString',
            $hero->heroSubHeading(),
        );

        self::assertSame(
            'testGetString',
            $hero->heroParagraph(),
        );

        self::assertTrue($hero->useShortHero());

        $callsExceptLink = $this->calls;

        $linkCall = $callsExceptLink[6];

        unset($callsExceptLink[6]);

        $callsExceptLink = array_values($callsExceptLink);

        self::assertSame(
            [
                [
                    'object' => 'AssetsFieldHandler',
                    'method' => 'getOneOrNull',
                    'element' => $this->entry,
                    'field' => 'heroImage',
                ],
                [
                    'object' => 'LinkFieldHandler',
                    'method' => 'getModel',
                    'element' => $this->entry,
                    'field' => 'heroUpperCta',
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getString',
                    'element' => $this->entry,
                    'field' => 'heroHeading',
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getString',
                    'element' => $this->entry,
                    'field' => 'heroSubheading',
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getString',
                    'element' => $this->entry,
                    'field' => 'heroParagraph',
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getBoolean',
                    'element' => $this->entry,
                    'field' => 'useShortHero',
                ],
                [
                    'object' => 'GetDefaultHeroOverlayOpacity',
                    'method' => 'getDefaultHeroOverlayOpacity',
                ],
                [
                    'object' => 'GetDefaultHeroImageUrl',
                    'method' => 'getDefaultHeroImageUrl',
                ],
            ],
            $callsExceptLink,
        );

        self::assertCount(3, $linkCall);

        self::assertSame('LinkFactory', $linkCall['object']);

        self::assertSame(
            'fromLinkFieldModel',
            $linkCall['method'],
        );

        $linkFieldModel = $linkCall['linkFieldModel'];

        assert($linkFieldModel instanceof LinkFieldModel);

        self::assertSame(
            'testLink',
            $linkFieldModel->getLink(),
        );
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testCreateFromEntryWhenHeroImage(): void
    {
        $this->assetHandlerReturnsAsset = true;

        $hero = $this->factory->createFromEntry(entry: $this->entry);

        $newHeroHeading = $hero->withHeroHeading(value: 'Test Val');

        self::assertSame(
            'Test Val',
            $newHeroHeading->heroHeading(),
        );

        self::assertSame(587, $hero->heroOverlayOpacity());

        self::assertSame(
            'testAssetUrl',
            $hero->heroImageUrl(),
        );

        self::assertSame(
            'testLinkContent',
            $hero->upperCta()->content(),
        );

        self::assertSame(
            'testLinkHref',
            $hero->upperCta()->href(),
        );

        self::assertSame(
            'testGetString',
            $hero->heroHeading(),
        );

        self::assertSame(
            'testGetString',
            $hero->heroSubHeading(),
        );

        self::assertSame(
            'testGetString',
            $hero->heroParagraph(),
        );

        self::assertTrue($hero->useShortHero());

        $callsExceptLast = $this->calls;

        $lastCall = array_pop($callsExceptLast);

        self::assertSame(
            [
                [
                    'object' => 'AssetsFieldHandler',
                    'method' => 'getOneOrNull',
                    'element' => $this->entry,
                    'field' => 'heroImage',
                ],
                [
                    'object' => 'LinkFieldHandler',
                    'method' => 'getModel',
                    'element' => $this->entry,
                    'field' => 'heroUpperCta',
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getString',
                    'element' => $this->entry,
                    'field' => 'heroHeading',
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getString',
                    'element' => $this->entry,
                    'field' => 'heroSubheading',
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getString',
                    'element' => $this->entry,
                    'field' => 'heroParagraph',
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getBoolean',
                    'element' => $this->entry,
                    'field' => 'useShortHero',
                ],
                [
                    'object' => 'GenericHandler',
                    'method' => 'getBoolean',
                    'element' => $this->entry,
                    'field' => 'heroDarkeningOverlayOpacity',
                ],
            ],
            $callsExceptLast,
        );

        self::assertCount(3, $lastCall);

        self::assertSame('LinkFactory', $lastCall['object']);

        self::assertSame(
            'fromLinkFieldModel',
            $lastCall['method'],
        );

        $linkFieldModel = $lastCall['linkFieldModel'];

        assert($linkFieldModel instanceof LinkFieldModel);

        self::assertSame(
            'testLink',
            $linkFieldModel->getLink(),
        );
    }
}
