<?php
declare(strict_types=1);

namespace App;

use lucidtaz\yii2whoops\ErrorHandler;

/**
 * @codeCoverageIgnore
 */
class CustomErrorHandler extends ErrorHandler
{
    /**
     * If this isn't here, Yii gets cranky
     */
    public $errorAction;
}
