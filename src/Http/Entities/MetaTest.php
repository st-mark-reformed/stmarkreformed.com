<?php

declare(strict_types=1);

namespace App\Http\Entities;

use PHPUnit\Framework\TestCase;

class MetaTest extends TestCase
{
    public function testMeta(): void
    {
        $meta = new Meta(metaTitle: 'testMetaTitle');

        self::assertSame(
            'testMetaTitle',
            $meta->metaTitle(),
        );

        self::assertSame(
            'St. Mark Reformed Church',
            $meta->siteName(),
        );

        self::assertSame(
            [],
            $meta->stylesheets(),
        );

        self::assertSame(
            ['https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js'],
            $meta->jsFiles(),
        );
    }
}
