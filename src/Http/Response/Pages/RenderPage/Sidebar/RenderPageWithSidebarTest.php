<?php

declare(strict_types=1);

namespace App\Http\Response\Pages\RenderPage\Sidebar;

use App\Http\Components\Hero\Hero;
use App\Http\Entities\Meta;
use App\Shared\Entries\EntryHandler;
use craft\elements\Entry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Markup;

class RenderPageWithSidebarTest extends TestCase
{
    private RenderPageWithSidebar $renderPage;

    /** @var Meta&MockObject */
    private mixed $meta;

    /** @var Hero&MockObject */
    private mixed $hero;

    /**
     * @var Entry&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $entry;

    /**
     * @var Entry&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $rootEntry;

    /** @var mixed[] */
    private array $twigCalls = [];

    /** @var mixed[] */
    private array $entryHandlerCalls = [];

    /** @var mixed[] */
    private array $buildSidebarCalls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->twigCalls = [];

        $this->meta = $this->createMock(Meta::class);

        $this->hero = $this->createMock(Hero::class);

        $this->entry = $this->createMock(Entry::class);

        $this->rootEntry = $this->createMock(Entry::class);

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

        $entryHandler = $this->createMock(
            EntryHandler::class,
        );

        $entryHandler->method('getRootEntry')->willReturnCallback(
            function (Entry $entry): Entry {
                $this->entryHandlerCalls[] = [
                    'method' => 'getRootEntry',
                    'entry' => $entry,
                ];

                return $this->rootEntry;
            }
        );

        $buildSidebar = $this->createMock(
            BuildSidebar::class,
        );

        $buildSidebar->method('fromRootEntry')->willReturnCallback(
            function (Entry $rootEntry, Entry $activeEntry): Markup {
                $this->buildSidebarCalls[] = [
                    'method' => 'fromRootEntry',
                    'rootEntry' => $rootEntry,
                    'activeEntry' => $activeEntry,
                ];

                return new Markup(
                    'sidebarMarkup',
                    'UTF-8',
                );
            }
        );

        $this->renderPage = new RenderPageWithSidebar(
            meta: $this->meta,
            hero: $this->hero,
            entry: $this->entry,
            contentString: 'testContentString',
            twig: $twig,
            entryHandler: $entryHandler,
            buildSidebar: $buildSidebar,
        );
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testRender(): void
    {
        self::assertSame(
            'testTwigRenderedString',
            $this->renderPage->render(),
        );

        self::assertCount(1, $this->twigCalls);

        self::assertSame(
            'render',
            $this->twigCalls[0]['method'],
        );

        self::assertSame(
            '@app/Http/Response/Pages/RenderPage/Sidebar/PageWithSidebar.twig',
            $this->twigCalls[0]['name'],
        );

        $context = $this->twigCalls['0']['context'];

        self::assertCount(4, $context);

        self::assertSame(
            $this->meta,
            $context['meta'],
        );

        self::assertSame(
            $this->hero,
            $context['hero'],
        );

        self::assertInstanceOf(
            Markup::class,
            $context['content']
        );

        self::assertSame(
            'testContentString',
            (string) $context['content'],
        );

        self::assertInstanceOf(
            Markup::class,
            $context['sideBarMarkup']
        );

        self::assertSame(
            'sidebarMarkup',
            (string) $context['sideBarMarkup'],
        );

        self::assertSame(
            [
                [
                    'method' => 'getRootEntry',
                    'entry' => $this->entry,
                ],
            ],
            $this->entryHandlerCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'fromRootEntry',
                    'rootEntry' => $this->rootEntry,
                    'activeEntry' => $this->entry,
                ],
            ],
            $this->buildSidebarCalls,
        );
    }
}
