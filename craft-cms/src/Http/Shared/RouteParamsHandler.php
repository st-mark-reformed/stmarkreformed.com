<?php

declare(strict_types=1);

namespace App\Http\Shared;

use BuzzingPixel\SlimBridge\ElementSetRoute\RouteParams;
use craft\elements\Entry;

use function assert;

class RouteParamsHandler
{
    /**
     * @phpstan-ignore-next-line
     */
    public function getEntry(RouteParams $routeParams): Entry
    {
        $entry = $routeParams->getParam('element');

        assert($entry instanceof Entry);

        return $entry;
    }
}
