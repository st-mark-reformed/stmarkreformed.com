<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\Entry;

use craft\base\Element;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;

use function assert;

class EntryFieldHandler
{
    /**
     * @return Entry[]
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

        assert($query instanceof EntryQuery);

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
    ): ?Entry {
        $query = $element->getFieldValue($field);

        assert($query instanceof EntryQuery);

        $entry = $query->one();

        assert($entry instanceof Entry || $entry === null);

        return $entry;
    }

    /**
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function getOne(
        Element $element,
        string $field,
    ): Entry {
        $entry = $this->getOneOrNull(
            element: $element,
            field: $field,
        );

        assert($entry instanceof Entry);

        return $entry;
    }
}
