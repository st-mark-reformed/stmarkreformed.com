<?php

declare(strict_types=1);

namespace App\Http\Response\Pages\RenderPage\Sidebar;

use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use PHPUnit\Framework\TestCase;
use Twig\Environment as TwigEnvironment;

use function assert;

class BuildSidebarTest extends TestCase
{
    private BuildSidebar $buildSidebar;

    /** @var mixed[] */
    private array $twigCalls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->twigCalls = [];

        $twig = $this->createMock(TwigEnvironment::class);

        $twig->method('render')->willReturnCallback(
            function (string $name, array $context): string {
                $this->twigCalls[] = [
                    'method' => 'render',
                    'name' => $name,
                    'context' => $context,
                ];

                return 'testTwigRenderedString';
            }
        );

        $this->buildSidebar = new BuildSidebar(twig: $twig);
    }

    public function testFromRootEntry(): void
    {
        $childEntry1 = $this->createMock(Entry::class);

        $childEntry1->title = 'Child 1';

        $childEntry1->method('getUrl')->willReturn(
            'child1Url'
        );

        $childEntry1->method('getId')->willReturn(1);

        $childEntry2 = $this->createMock(Entry::class);

        $childEntry2->title = 'Child 2';

        $childEntry2->method('getUrl')->willReturn(
            'child2Url'
        );

        $childEntry2->method('getId')->willReturn(2);

        $childEntry3 = $this->createMock(Entry::class);

        $childEntry3->title = 'Child 3';

        $childEntry3->method('getUrl')->willReturn(
            'child3Url'
        );

        $childEntry3->method('getId')->willReturn(3);

        $entryQuery = $this->createMock(EntryQuery::class);

        $entryQuery->method('all')->willReturn([
            $childEntry1,
            $childEntry2,
            $childEntry3,
        ]);

        $rootEntry = $this->createMock(Entry::class);

        $rootEntry->title = 'Root';

        $rootEntry->method('getUrl')->willReturn('rootUrl');

        $rootEntry->method('getId')->willReturn(999);

        $rootEntry->method('getChildren')->willReturn(
            $entryQuery,
        );

        self::assertSame(
            'testTwigRenderedString',
            (string) $this->buildSidebar->fromRootEntry(
                rootEntry: $rootEntry,
                activeEntry: $childEntry2,
            ),
        );

        self::assertCount(1, $this->twigCalls);

        self::assertSame(
            'render',
            $this->twigCalls[0]['method'],
        );

        self::assertSame(
            '@app/Http/Response/Pages/RenderPage/Sidebar/Sidebar.twig',
            $this->twigCalls[0]['name'],
        );

        $context = $this->twigCalls['0']['context'];

        self::assertCount(1, $context);

        $sidebarItems = $context['sidebarItems'];

        self::assertCount(4, $sidebarItems);

        $model0 = $sidebarItems[0];
        assert($model0 instanceof SidebarContentModel);
        self::assertSame(
            'Root',
            $model0->content(),
        );
        self::assertSame(
            'rootUrl',
            $model0->href(),
        );
        self::assertFalse($model0->active());

        $model1 = $sidebarItems[1];
        assert($model1 instanceof SidebarContentModel);
        self::assertSame(
            'Child 1',
            $model1->content(),
        );
        self::assertSame(
            'child1Url',
            $model1->href(),
        );
        self::assertFalse($model1->active());

        $model2 = $sidebarItems[2];
        assert($model2 instanceof SidebarContentModel);
        self::assertSame(
            'Child 2',
            $model2->content(),
        );
        self::assertSame(
            'child2Url',
            $model2->href(),
        );
        self::assertTrue($model2->active());

        $model3 = $sidebarItems[3];
        assert($model3 instanceof SidebarContentModel);
        self::assertSame(
            'Child 3',
            $model3->content(),
        );
        self::assertSame(
            'child3Url',
            $model3->href(),
        );
        self::assertFalse($model3->active());
    }
}
