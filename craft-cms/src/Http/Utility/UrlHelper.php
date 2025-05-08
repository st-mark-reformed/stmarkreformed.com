<?php

declare(strict_types=1);

namespace App\Http\Utility;

use craft\helpers\UrlHelper as CraftUrlHelper;
use yii\base\Exception;

/**
 * @codeCoverageIgnore
 */
class UrlHelper
{
    /**
     * Returns a site URL.
     *
     * @throws Exception
     *
     * @phpstan-ignore-next-line
     */
    public function siteUrl(
        string $path = '',
        array|string|null $params = null,
        ?string $scheme = null,
        ?int $siteId = null
    ): string {
        return CraftUrlHelper::siteUrl(
            $path,
            $params,
            $scheme,
            $siteId,
        );
    }
}
