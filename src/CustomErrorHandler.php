<?php

declare(strict_types=1);

namespace App;

use lucidtaz\yii2whoops\ErrorHandler;

// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint

/**
 * @codeCoverageIgnore
 * @psalm-suppress     MissingPropertyType
 * @psalm-suppress     PropertyNotSetInConstructor
 */
class CustomErrorHandler extends ErrorHandler
{
    /**
     * If this isn't here, Yii gets cranky
     *
     * @phpstan-ignore-next-line
     */
    public $errorAction;
}
