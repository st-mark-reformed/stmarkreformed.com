<?php

declare(strict_types=1);

namespace App\Http\PageBuilder;

use App\Http\PageBuilder\BlockResponse\BlockResponseBuilderFactory;
use craft\elements\MatrixBlock;
use yii\base\InvalidConfigException;

use function array_map;
use function implode;

class PageBuilderResponseCompiler
{
    public function __construct(
        private BlockResponseBuilderFactory $blockResponseBuilderFactory,
    ) {
    }

    /**
     * @param MatrixBlock[] $pageBuilderBlocks
     *
     * @throws InvalidConfigException
     */
    public function compile(array $pageBuilderBlocks): string
    {
        return implode(
            '',
            array_map(
                function (MatrixBlock $matrixBlock): string {
                    return $this->blockResponseBuilderFactory
                        ->make(matrixBlock: $matrixBlock)
                        ->buildResponse(matrixBlock: $matrixBlock);
                },
                $pageBuilderBlocks,
            ),
        );
    }
}
