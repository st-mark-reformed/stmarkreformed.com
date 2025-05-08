<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\Sidebar;

use App\Http\Components\Link\Link;
use App\Shared\ElementQueryFactories\CategoryQueryFactory;
use App\Shared\Testing\TestCase;
use craft\elements\Category;
use craft\elements\db\CategoryQuery;
use PHPUnit\Framework\MockObject\MockObject;

use function array_map;

class RetrieveMostRecentSeriesTest extends TestCase
{
    private RetrieveMostRecentSeries $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new RetrieveMostRecentSeries(
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

        $mock->method('limit')->willReturnCallback(
            $callback
        );

        $mock->method('__call')->willReturnCallback(
            $callback,
        );

        $mock->method('all')->willReturn([
            $series1,
            $series2,
        ]);

        return $mock;
    }

    public function testRetrieve(): void
    {
        self::assertSame(
            [
                [
                    'isEmpty' => false,
                    'content' => 'Test Title 1',
                    'href' => '/media/messages?series%5B0%5D=test-title-1',
                    'newWindow' => false,
                ],
                [
                    'isEmpty' => false,
                    'content' => 'Test Title 2',
                    'href' => '/media/messages?series%5B0%5D=test-title-2',
                    'newWindow' => false,
                ],
            ],
            array_map(
                static fn (Link $link) => [
                    'isEmpty' => $link->isEmpty(),
                    'content' => $link->content(),
                    'href' => $link->href(),
                    'newWindow' => $link->newWindow(),
                ],
                $this->service->retrieve(),
            ),
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
                    'args' => ['latestEntryAt desc'],
                ],
                [
                    'object' => 'CategoryQuery',
                    'method' => 'limit',
                    'args' => [6],
                ],
                [
                    'object' => 'CategoryQuery',
                    'method' => '__call',
                    'args' => [
                        'excludeFromFeatured',
                        [false],
                    ],
                ],
            ],
            $this->calls,
        );
    }
}
