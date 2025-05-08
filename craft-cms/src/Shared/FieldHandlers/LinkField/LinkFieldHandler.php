<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\LinkField;

use craft\base\Element;
use craft\errors\InvalidFieldException;
use typedlinkfield\models\Link as LinkFieldModel;

use function assert;

class LinkFieldHandler
{
    /**
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function getModel(
        Element $element,
        string $field,
    ): LinkFieldModel {
        $linkFieldModel = $element->getFieldValue($field);

        assert($linkFieldModel instanceof LinkFieldModel);

        return $linkFieldModel;
    }
}
