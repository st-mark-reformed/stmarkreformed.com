<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\ColourSwatches;

use craft\base\Element;
use craft\errors\InvalidFieldException;
use percipioglobal\colourswatches\models\ColourSwatches;
use stdClass;

use function assert;
use function is_array;

class ColorOptionFromElementField
{
    /**
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function getStringValue(
        Element $element,
        string $fieldName,
        string $option,
    ): string {
        $colorModel = $element->getFieldValue($fieldName);

        assert($colorModel instanceof ColourSwatches);

        $colors = $colorModel->colors();

        assert(is_array($colors));

        $colorOption = $colors[0];

        assert($colorOption instanceof stdClass);

        return (string) $colorOption->{$option};
    }
}
