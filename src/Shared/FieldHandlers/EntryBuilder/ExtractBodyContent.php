<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\EntryBuilder;

use App\Shared\FieldHandlers\Generic\GenericHandler;
use App\Shared\FieldHandlers\Matrix\MatrixFieldHandler;
use craft\base\Element;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;
use yii\base\InvalidConfigException;

use function array_map;
use function implode;

class ExtractBodyContent
{
    public function __construct(
        private GenericHandler $genericHandler,
        private MatrixFieldHandler $matrixFieldHandler,
    ) {
    }

    /**
     * @throws InvalidFieldException
     * @throws InvalidConfigException
     *
     * @phpstan-ignore-next-line
     */
    public function fromElementWithEntryBuilder(Element $element): string
    {
        $blocks = $this->matrixFieldHandler->getAll(
            element: $element,
            field: 'entryBuilder',
        );

        $contents = array_map(
            function (MatrixBlock $block): string {
                if ($block->getType()->handle !== 'basicEntryBlock') {
                    return '';
                }

                return $this->genericHandler->getString(
                    element: $block,
                    field: 'body',
                );
            },
            $blocks,
        );

        return implode('', $contents);
    }
}
