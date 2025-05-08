<?php

declare(strict_types=1);

namespace App\Shared\FieldHandlers\Assets;

use craft\base\Element;
use craft\elements\Asset;
use craft\elements\db\AssetQuery;
use craft\errors\InvalidFieldException;

use function assert;

class AssetsFieldHandler
{
    /**
     * @return Asset[]
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

        assert($query instanceof AssetQuery);

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
    ): ?Asset {
        $query = $element->getFieldValue($field);

        assert($query instanceof AssetQuery);

        $asset = $query->one();

        assert($asset instanceof Asset || $asset === null);

        return $asset;
    }

    /**
     * @throws InvalidFieldException
     *
     * @phpstan-ignore-next-line
     */
    public function getOne(
        Element $element,
        string $field,
    ): Asset {
        $asset = $this->getOneOrNull(
            element: $element,
            field: $field,
        );

        assert($asset instanceof Asset);

        return $asset;
    }
}
