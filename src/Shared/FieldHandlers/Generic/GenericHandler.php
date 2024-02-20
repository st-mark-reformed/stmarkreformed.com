<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\Generic;

use craft\base\Element;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use DateTimeImmutable;
use DateTimeInterface;
use Twig\Markup;

use function assert;

class GenericHandler
{
    /**
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function getTwigMarkup(
        Element $element,
        string $field,
    ): Markup {
        $markup = $element->getFieldValue($field);

        assert($markup instanceof Markup || $markup === null);

        if ($markup === null) {
            return new Markup(
                '',
                'UTF-8',
            );
        }

        return $markup;
    }

    /**
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function getString(
        Element $element,
        string $field,
    ): string {
        return (string) $element->getFieldValue($field);
    }

    /**
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function getInt(
        Element $element,
        string $field,
    ): int {
        return (int) $element->getFieldValue($field);
    }

    /**
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function getBoolean(
        Element $element,
        string $field,
    ): bool {
        return (bool) $element->getFieldValue($field);
    }

    /**
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function getDate(
        Element $element,
        string $field,
    ): DateTimeInterface {
        $value = $element->getFieldValue($field);

        assert($value instanceof DateTimeInterface);

        return $value;
    }

    public function entryPostDate(Entry $entry): DateTimeImmutable
    {
        $date = $entry->postDate;

        assert($date instanceof DateTimeInterface);

        return DateTimeImmutable::createFromMutable($date);
    }
}
