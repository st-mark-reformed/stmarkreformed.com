<?php

declare(strict_types=1);

namespace App\Http\Response\Pages\RenderPage;

use App\Http\Components\Hero\HeroFactory;
use App\Http\Entities\Meta;
use App\Http\PageBuilder\PageBuilderResponseCompiler;
use App\Http\Response\Pages\RenderPage\Sidebar\BuildSidebar;
use App\Http\Response\Pages\RenderPage\Sidebar\RenderPageWithSidebar;
use App\Http\Response\Pages\RenderPage\Standard\RenderStandardPage;
use App\Shared\Entries\EntryHandler;
use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\Matrix\MatrixFieldHandler;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use Twig\Environment as TwigEnvironment;
use yii\base\InvalidConfigException;

class RenderPageFactory
{
    public function __construct(
        private TwigEnvironment $twig,
        private HeroFactory $heroFactory,
        private EntryHandler $entryHandler,
        private BuildSidebar $buildSidebar,
        private GenericHandler $genericHandler,
        private MatrixFieldHandler $matrixFieldHandler,
        private PageBuilderResponseCompiler $pageBuilderResponseCompiler,
    ) {
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     */
    public function make(
        Entry $entry,
    ): RenderPageContract {
        $showSubPageSidebar = $this->genericHandler->getBoolean(
            element: $entry,
            field: 'showSubPageSidebar',
        );

        $meta = new Meta();

        $pageBuilderBlocks = $this->matrixFieldHandler->getAll(
            element: $entry,
            field: 'pageBuilder',
        );

        $contentString = $this->pageBuilderResponseCompiler->compile(
            pageBuilderBlocks: $pageBuilderBlocks,
        );

        $hero = $this->heroFactory->createFromEntry(entry: $entry);

        if ($showSubPageSidebar) {
            return new RenderPageWithSidebar(
                hero: $hero,
                meta: $meta,
                entry: $entry,
                twig: $this->twig,
                contentString: $contentString,
                entryHandler: $this->entryHandler,
                buildSidebar: $this->buildSidebar,
            );
        }

        return new RenderStandardPage(
            hero: $hero,
            meta: $meta,
            twig: $this->twig,
            contentString: $contentString,
        );
    }
}
