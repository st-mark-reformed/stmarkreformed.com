<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\Categories;

use craft\base\Element;
use craft\elements\Category;
use craft\elements\db\CategoryQuery;
use craft\errors\InvalidFieldException;

use function assert;

class CategoriesFieldHandler
{
    /**
     * @return Category[]
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

        assert($query instanceof CategoryQuery);

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
    ): ?Category {
        $query = $element->getFieldValue($field);

        assert($query instanceof CategoryQuery);

        $category = $query->one();

        assert($category instanceof Category || $category === null);

        return $category;
    }

    /**
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function getOne(
        Element $element,
        string $field,
    ): Category {
        $category = $this->getOneOrNull(
            element: $element,
            field: $field,
        );

        assert($category instanceof Category);

        return $category;
    }
}
