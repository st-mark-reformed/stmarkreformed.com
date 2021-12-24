<?php

declare(strict_types=1);

namespace App\Messages\SetMessageSeriesLatestEntryDate;

use App\Shared\ElementQueryFactories\CategoryQueryFactory;
use App\Shared\Testing\TestCase;
use craft\elements\Category;
use craft\elements\db\CategoryQuery;
use PHPUnit\Framework\MockObject\MockObject;

use function assert;
use function is_array;

class SetMessageSeriesLatestEntryTest extends TestCase
{
    private SetMessageSeriesLatestEntry $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new SetMessageSeriesLatestEntry(
            categoryQueryFactory: $this->mockCategoryQueryFactory(),
            setLatestEntryForCategory: $this->mockSetLatestEntryForCategory(),
        );
    }

    /**
     * @return CategoryQueryFactory&MockObject
     */
    private function mockCategoryQueryFactory(): mixed
    {
        $factory = $this->createMock(
            CategoryQueryFactory::class,
        );

        $factory->method(self::anything())->willReturnCallback(
            function (): CategoryQuery {
                return $this->genericCall(
                    object: 'CategoryQueryFactory',
                    return: $this->mockCategoryQuery(),
                );
            }
        );

        return $factory;
    }

    /**
     * @return CategoryQuery&MockObject
     *
     * @phpstan-ignore-next-line
     */
    private function mockCategoryQuery(): mixed
    {
        $query = $this->createMock(CategoryQuery::class);

        $cat1       = $this->createMock(Category::class);
        $cat1->slug = 'cat1slug';

        $cat2       = $this->createMock(Category::class);
        $cat2->slug = 'cat2slug';

        $query->method('group')->willReturnCallback(
            function () use ($query): CategoryQuery {
                return $this->genericCall(
                    object: 'CategoryQuery',
                    return: $query,
                );
            }
        );

        $query->method('all')->willReturnCallback(
            function () use ($cat1, $cat2): array {
                return $this->genericCall(
                    object: 'CategoryQuery',
                    return: [$cat1, $cat2],
                );
            }
        );

        return $query;
    }

    /**
     * @return SetLatestMessageForSeries&MockObject
     */
    private function mockSetLatestEntryForCategory(): mixed
    {
        $set = $this->createMock(
            SetLatestMessageForSeries::class
        );

        $set->method(self::anything())->willReturnCallback(
            function (): void {
                $this->genericCall(object: 'SetLatestMessageForSeries');
            }
        );

        return $set;
    }

    public function testSet(): void
    {
        $this->service->set();

        self::assertCount(5, $this->calls);

        self::assertSame(
            [
                'object' => 'CategoryQueryFactory',
                'method' => 'make',
                'args' => [],
            ],
            $this->calls[0],
        );

        self::assertSame(
            [
                'object' => 'CategoryQuery',
                'method' => 'group',
                'args' => ['messageSeries'],
            ],
            $this->calls[1],
        );

        self::assertSame(
            [
                'object' => 'CategoryQuery',
                'method' => 'all',
                'args' => [],
            ],
            $this->calls[2],
        );

        self::assertSame(
            'SetLatestMessageForSeries',
            $this->calls[3]['object'],
        );

        self::assertSame(
            'set',
            $this->calls[3]['method'],
        );

        $call4Args = $this->calls[3]['args'];

        assert(is_array($call4Args));

        self::assertCount(1, $call4Args);

        $call4Category = $call4Args[0];

        assert($call4Category instanceof Category);

        self::assertSame(
            'cat1slug',
            $call4Category->slug,
        );

        $call5Args = $this->calls[4]['args'];

        assert(is_array($call5Args));

        self::assertCount(1, $call5Args);

        $call5Category = $call5Args[0];

        assert($call5Category instanceof Category);

        self::assertSame(
            'cat2slug',
            $call5Category->slug,
        );
    }
}
