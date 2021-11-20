<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\SearchForm;

use App\Shared\ElementQueryFactories\CategoryQueryFactory;
use App\Shared\Testing\TestCase;
use craft\elements\Category;
use craft\elements\db\CategoryQuery;
use PHPUnit\Framework\MockObject\MockObject;

class RetrieveSeriesOptionsTest extends TestCase
{
    private RetrieveSeriesOptions $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new RetrieveSeriesOptions(
            queryFactory: $this->mockCategoryQueryFactory(),
        );
    }

    /**
     * @return CategoryQueryFactory&MockObject
     */
    private function mockCategoryQueryFactory(): mixed
    {
        $mock = $this->createMock(
            CategoryQueryFactory::class,
        );

        $mock->method('make')->willReturn(
            $this->mockCategoryQuery(),
        );

        return $mock;
    }

    /**
     * @return CategoryQuery&MockObject
     *
     * @phpstan-ignore-next-line
     */
    private function mockCategoryQuery(): mixed
    {
        $series1 = $this->createMock(Category::class);

        $series1->title = 'Test Title 1';

        $series1->slug = 'test-title-1';

        $series2 = $this->createMock(Category::class);

        $series2->title = 'Test Title 2';

        $series2->slug = 'test-title-2';

        $mock = $this->createMock(CategoryQuery::class);

        $callback = function () use ($mock): CategoryQuery {
            return $this->genericCall(
                object: 'CategoryQuery',
                return: $mock,
            );
        };

        $mock->method('group')->willReturnCallback(
            $callback,
        );

        $mock->method('orderBy')->willReturnCallback(
            $callback
        );

        $mock->method('all')->willReturn([
            $series1,
            $series2,
        ]);

        return $mock;
    }

    public function testRetrieve(): void
    {
        $optionGroup = $this->service->retrieve([
            'fooBar',
            'test-title-2',
        ]);

        self::assertSame(
            [
                'title' => '',
                'options' => [
                    [
                        'name' => 'Test Title 1',
                        'slug' => 'test-title-1',
                        'isActive' => false,
                    ],
                    [
                        'name' => 'Test Title 2',
                        'slug' => 'test-title-2',
                        'isActive' => true,
                    ],
                ],
            ],
            [
                'title' => $optionGroup->groupTitle(),
                'options' => $optionGroup->map(
                    static fn (SelectOption $option) => [
                        'name' => $option->name(),
                        'slug' => $option->slug(),
                        'isActive' => $option->isActive(),
                    ],
                ),
            ],
        );

        self::assertSame(
            [
                [
                    'object' => 'CategoryQuery',
                    'method' => 'group',
                    'args' => ['messageSeries'],
                ],
                [
                    'object' => 'CategoryQuery',
                    'method' => 'orderBy',
                    'args' => ['title asc'],
                ],
            ],
            $this->calls,
        );
    }
}
