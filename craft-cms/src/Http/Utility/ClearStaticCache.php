<?php

declare(strict_types=1);

namespace App\Http\Utility;

use BuzzingPixel\StaticCache\CacheApi\CacheApiContract;

class ClearStaticCache
{
    public function __construct(private CacheApiContract $staticCacheApi)
    {
    }

    public function clear(): void
    {
        $this->staticCacheApi->clearAllCache();
    }
}
