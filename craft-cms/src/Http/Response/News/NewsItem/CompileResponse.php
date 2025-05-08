<?php

declare(strict_types=1);

namespace App\Http\Response\News\NewsItem;

use App\Http\PageBuilder\PageBuilderResponseCompiler;
use App\Shared\FieldHandlers\Matrix\MatrixFieldHandler;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use yii\base\InvalidConfigException;

class CompileResponse
{
    public function __construct(
        private MatrixFieldHandler $matrixFieldHandler,
        private PageBuilderResponseCompiler $pageBuilderResponseCompiler,
    ) {
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     *
     * @phpstan-ignore-next-line
     */
    public function fromEntry(Entry $entry): string
    {
        $pageBuilderBlocks = $this->matrixFieldHandler->getAll(
            element: $entry,
            field: 'entryBuilder',
        );

        return $this->pageBuilderResponseCompiler->compile(
            pageBuilderBlocks: $pageBuilderBlocks,
        );
    }
}
