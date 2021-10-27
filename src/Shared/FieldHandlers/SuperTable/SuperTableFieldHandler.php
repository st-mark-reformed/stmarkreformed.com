<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\SuperTable;

use craft\base\Element;
use craft\errors\InvalidFieldException;
use verbb\supertable\elements\db\SuperTableBlockQuery;
use verbb\supertable\elements\SuperTableBlockElement;

use function assert;

/**
 * @psalm-suppress MixedReturnTypeCoercion
 */
class SuperTableFieldHandler
{
    /**
     * @return SuperTableBlockElement[]
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

        assert($query instanceof SuperTableBlockQuery);

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
    ): ?SuperTableBlockElement {
        $query = $element->getFieldValue($field);

        assert($query instanceof SuperTableBlockQuery);

        $block = $query->one();

        assert(
            $block instanceof SuperTableBlockElement ||
            $block === null
        );

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
    ): SuperTableBlockElement {
        $block = $this->getOneOrNull(
            element: $element,
            field: $field,
        );

        assert($block instanceof SuperTableBlockElement);

        return $block;
    }
}
