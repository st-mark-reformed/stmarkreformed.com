<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\Generic;

use craft\base\Element;
use craft\errors\InvalidFieldException;
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

        assert($markup instanceof Markup);

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
    public function getBoolean(
        Element $element,
        string $field,
    ): bool {
        return (bool) $element->getFieldValue($field);
    }
}
