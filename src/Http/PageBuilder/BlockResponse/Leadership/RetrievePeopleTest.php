<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\Leadership;

use App\Shared\ElementQueryFactories\EntryQueryFactory;
use App\Shared\FieldHandlers\Assets\AssetsFieldHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use craft\base\Element;
use craft\elements\Asset;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Markup;

use function count;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 * @psalm-suppress MixedArgument
 * @psalm-suppress MixedMethodCall
 */
class RetrievePeopleTest extends TestCase
{
    private RetrievePeople $service;

    /** @var mixed[] */
    private array $genericHandlerCalls = [];

    /** @var mixed[] */
    private array $assetsFieldHandlerCalls = [];

    /**
     * @var Entry&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $entry1;

    /**
     * @var Entry&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $entry2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new RetrievePeople(
            genericHandler: $this->mockGenericHandler(),
            entryQueryFactory: $this->mockEntryQueryFactory(),
            assetsFieldHandler: $this->mockAssetsFieldHandler(),
        );
    }

    /**
     * @return MockObject&GenericHandler
     */
    private function mockGenericHandler(): mixed
    {
        $handler = $this->createMock(GenericHandler::class);

        $handler->method('getTwigMarkup')->willReturnCallback(
            function (Element $element, string $field): Markup {
                $this->genericHandlerCalls[] = [
                    'method' => 'getTwigMarkup',
                    'element' => $element,
                    'field' => $field,
                ];

                return new Markup(
                    'twigMarkupString',
                    'UTf-8',
                );
            }
        );

        return $handler;
    }

    /**
     * @return MockObject&EntryQueryFactory
     */
    private function mockEntryQueryFactory(): mixed
    {
        $query = $this->createMock(EntryQuery::class);

        $query->method('section')->willReturnCallback(
            function (string $value) use (
                $query,
            ): EntryQuery {
                $this->genericHandlerCalls[] = [
                    'method' => 'section',
                    'value' => $value,
                ];

                return $query;
            }
        );

        $query->method('__call')->willReturnCallback(
            function (
                string $name,
                array $params
            ) use (
                $query,
            ): EntryQuery {
                $this->genericHandlerCalls[] = [
                    'method' => '__call',
                    'name' => $name,
                    'params' => $params,
                ];

                return $query;
            }
        );

        $this->entry1 = $this->createMock(Entry::class);

        $this->entry1->method('__call')->willReturnCallback(
            static function (string $name): string {
                /** @phpstan-ignore-next-line */
                return match ($name) {
                    'fullNameHonorific' => 'Entry 1 Title',
                };
            }
        );

        $this->entry2 = $this->createMock(Entry::class);

        $this->entry2->method('__call')->willReturnCallback(
            static function (string $name): string {
                /** @phpstan-ignore-next-line */
                return match ($name) {
                    'fullNameHonorific' => 'Entry 2 Title',
                };
            }
        );

        $query->method('all')->willReturn([
            $this->entry1,
            $this->entry2,
        ]);

        $factory = $this->createMock(
            EntryQueryFactory::class,
        );

        $factory->method('make')->willReturn($query);

        return $factory;
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
                $this->assetsFieldHandlerCalls[] = [
                    'method' => 'getOneOrNull',
                    'element' => $element,
                    'field' => $field,
                ];

                if (count($this->assetsFieldHandlerCalls) > 1) {
                    return null;
                }

                $asset = $this->createMock(Asset::class);

                $asset->method('getUrl')
                    ->willReturn('testUrl');

                return $asset;
            }
        );

        return $handler;
    }

    public function testRetrievePeople(): void
    {
        $models = $this->service->retrieve(position: 'testPosition');

        self::assertCount(2, $models);

        self::assertSame(
            'testUrl',
            $models[0]->imageUrl()
        );

        self::assertSame(
            'Entry 1 Title',
            $models[0]->title()
        );

        self::assertSame(
            'twigMarkupString',
            (string) $models[0]->content()
        );

        self::assertSame(
            '',
            $models[1]->imageUrl()
        );

        self::assertSame(
            'Entry 2 Title',
            $models[1]->title()
        );

        self::assertSame(
            'twigMarkupString',
            (string) $models[1]->content()
        );

        self::assertSame(
            [
                [
                    'method' => 'section',
                    'value' => 'profiles',
                ],
                [
                    'method' => '__call',
                    'name' => 'leadershipPosition',
                    'params' => ['testPosition'],
                ],
                [
                    'method' => 'getTwigMarkup',
                    'element' => $this->entry1,
                    'field' => 'bio',
                ],
                [
                    'method' => 'getTwigMarkup',
                    'element' => $this->entry2,
                    'field' => 'bio',
                ],
            ],
            $this->genericHandlerCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'getOneOrNull',
                    'element' => $this->entry1,
                    'field' => 'profilePhoto',
                ],
                [
                    'method' => 'getOneOrNull',
                    'element' => $this->entry2,
                    'field' => 'profilePhoto',
                ],
            ],
            $this->assetsFieldHandlerCalls,
        );
    }
}
