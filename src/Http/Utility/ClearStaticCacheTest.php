<?php

declare(strict_types=1);

namespace App\Http\Utility;

use BuzzingPixel\StaticCache\CacheApi\CacheApiContract;
use PHPUnit\Framework\TestCase;

class ClearStaticCacheTest extends TestCase
{
    public function testClear(): void
    {
        parent::setUp();

        $staticCacheApi = $this->createMock(
            CacheApiContract::class,
        );

        $staticCacheApi->expects(self::once())
            ->method('clearAllCache');

        $service = new ClearStaticCache(staticCacheApi: $staticCacheApi);

        $service->clear();
    }
}
