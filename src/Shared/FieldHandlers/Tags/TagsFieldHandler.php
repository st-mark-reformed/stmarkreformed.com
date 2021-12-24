<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\Tags;

use craft\base\Element;
use craft\elements\db\TagQuery;
use craft\elements\Tag;
use craft\errors\InvalidFieldException;

use function assert;

class TagsFieldHandler
{
    /**
     * @return Tag[]
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

        assert($query instanceof TagQuery);

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
    ): ?Tag {
        $query = $element->getFieldValue($field);

        assert($query instanceof TagQuery);

        $tag = $query->one();

        assert($tag instanceof Tag || $tag === null);

        return $tag;
    }

    /**
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function getOne(
        Element $element,
        string $field,
    ): Tag {
        $tag = $this->getOneOrNull(
            element: $element,
            field: $field,
        );

        assert($tag instanceof Tag);

        return $tag;
    }
}
