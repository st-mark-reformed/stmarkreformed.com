<?php

declare(strict_types=1);

namespace App\Http\Response\Pages\RenderPage;

use App\Http\Components\Hero\Hero;
use App\Http\Components\Hero\HeroFactory;
use App\Http\PageBuilder\PageBuilderResponseCompiler;
use App\Http\Response\Pages\RenderPage\Sidebar\BuildSidebar;
use App\Http\Response\Pages\RenderPage\Sidebar\RenderPageWithSidebar;
use App\Http\Response\Pages\RenderPage\Standard\RenderStandardPage;
use App\Shared\Entries\EntryHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\Matrix\MatrixFieldHandler;
use craft\base\Element;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment as TwigEnvironment;
use yii\base\InvalidConfigException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 * @psalm-suppress MixedArgument
 */
class RenderPageFactoryTest extends TestCase
{
    private RenderPageFactory $factory;

    /**
     * @var Entry&MockObject
     * @phpstan-ignore-next-line
     */
    private mixed $entry;

    /** @var Hero&MockObject */
    private mixed $hero;

    /** @var mixed[] */
    private array $heroFactoryCalls = [];

    private bool $getBooleanReturnValue = false;

    /** @var mixed[] */
    private array $getBooleanCalls = [];

    /** @var MatrixBlock[] */
    private array $pageBuilderBlocks;

    /** @var mixed[] */
    private array $matrixFieldHandlerCalls = [];

    /** @var mixed[] */
    private array $pageBuilderResponseCompilerCalls = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->heroFactoryCalls = [];

        $this->getBooleanReturnValue = false;

        $this->getBooleanCalls = [];

        $this->matrixFieldHandlerCalls = [];

        $this->pageBuilderResponseCompilerCalls = [];

        $this->entry = $this->createMock(Entry::class);

        $this->hero = $this->createMock(Hero::class);

        $heroFactory = $this->createMock(HeroFactory::class);

        $heroFactory->method('createFromEntry')->willReturnCallback(
            function (Entry $entry): Hero {
                $this->heroFactoryCalls[] = [
                    'method' => 'createFromEntry',
                    'entry' => $entry,
                ];

                return $this->hero;
            }
        );

        $genericHandler = $this->createMock(
            GenericHandler::class,
        );

        $genericHandler->method('getBoolean')->willReturnCallback(
            function (Element $element, string $field): bool {
                $this->getBooleanCalls[] = [
                    'element' => $element,
                    'field' => $field,
                ];

                return $this->getBooleanReturnValue;
            }
        );

        $this->pageBuilderBlocks = [
            $this->createMock(
                MatrixBlock::class,
            ),
        ];

        $matrixFieldHandler = $this->createMock(
            MatrixFieldHandler::class,
        );

        $matrixFieldHandler->method('getAll')->willReturnCallback(
            function (Element $element, string $field): array {
                $this->matrixFieldHandlerCalls[] = [
                    'method' => 'getAll',
                    'element' => $element,
                    'field' => $field,
                ];

                return $this->pageBuilderBlocks;
            }
        );

        $pageBuilderResponseCompiler = $this->createMock(
            PageBuilderResponseCompiler::class,
        );

        $pageBuilderResponseCompiler->method('compile')
            ->willReturnCallback(
                function (array $pageBuilderBlocks): string {
                    $this->pageBuilderResponseCompilerCalls[] = [
                        'method' => 'compile',
                        'pageBuilderBlocks' => $pageBuilderBlocks,
                    ];

                    return 'compilerContentString';
                }
            );

        $this->factory = new RenderPageFactory(
            twig: $this->createMock(TwigEnvironment::class),
            heroFactory: $heroFactory,
            entryHandler: $this->createMock(
                EntryHandler::class,
            ),
            buildSidebar: $this->createMock(
                BuildSidebar::class,
            ),
            genericHandler: $genericHandler,
            matrixFieldHandler: $matrixFieldHandler,
            pageBuilderResponseCompiler: $pageBuilderResponseCompiler,
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws InvalidFieldException
     */
    public function testMakeShowSubPageSidebar(): void
    {
        $this->getBooleanReturnValue = true;

        $renderPageReturn = $this->factory->make(entry: $this->entry);

        self::assertInstanceOf(
            RenderPageWithSidebar::class,
            $renderPageReturn,
        );

        self::assertSame(
            [
                [
                    'method' => 'createFromEntry',
                    'entry' => $this->entry,
                ],
            ],
            $this->heroFactoryCalls,
        );

        self::assertSame(
            [
                [
                    'element' => $this->entry,
                    'field' => 'showSubPageSidebar',
                ],
            ],
            $this->getBooleanCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'getAll',
                    'element' => $this->entry,
                    'field' => 'pageBuilder',
                ],
            ],
            $this->matrixFieldHandlerCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'compile',
                    'pageBuilderBlocks' => $this->pageBuilderBlocks,
                ],
            ],
            $this->pageBuilderResponseCompilerCalls,
        );
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function testMakeStandardPage(): void
    {
        $this->getBooleanReturnValue = false;

        $renderPageReturn = $this->factory->make(entry: $this->entry);

        self::assertInstanceOf(
            RenderStandardPage::class,
            $renderPageReturn,
        );

        self::assertSame(
            [
                [
                    'method' => 'createFromEntry',
                    'entry' => $this->entry,
                ],
            ],
            $this->heroFactoryCalls,
        );

        self::assertSame(
            [
                [
                    'element' => $this->entry,
                    'field' => 'showSubPageSidebar',
                ],
            ],
            $this->getBooleanCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'getAll',
                    'element' => $this->entry,
                    'field' => 'pageBuilder',
                ],
            ],
            $this->matrixFieldHandlerCalls,
        );

        self::assertSame(
            [
                [
                    'method' => 'compile',
                    'pageBuilderBlocks' => $this->pageBuilderBlocks,
                ],
            ],
            $this->pageBuilderResponseCompilerCalls,
        );
    }
}
