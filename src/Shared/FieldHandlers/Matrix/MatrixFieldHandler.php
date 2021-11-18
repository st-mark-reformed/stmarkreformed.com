<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\Matrix;

use craft\base\Element;
use craft\elements\db\MatrixBlockQuery;
use craft\elements\MatrixBlock;
use craft\errors\InvalidFieldException;

use function assert;

class MatrixFieldHandler
{
    /**
     * @return MatrixBlock[]
     *
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function getAll(
        Element $element,
        string $field,
    ): array {
        $query = $element->getFieldValue($field);

        assert($query instanceof MatrixBlockQuery);

        return $query->all();
    }

    /**
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function getOneOrNull(
        Element $element,
        string $field,
    ): ?MatrixBlock {
        $query = $element->getFieldValue($field);

        assert($query instanceof MatrixBlockQuery);

        $block = $query->one();

        assert($block instanceof MatrixBlock || $block === null);

        return $block;
    }

    /**
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function getOne(
        Element $element,
        string $field,
    ): MatrixBlock {
        $block = $this->getOneOrNull(
            element: $element,
            field: $field,
        );

        assert($block instanceof MatrixBlock);

        return $block;
    }
}
