<?php

declare(strict_types=1);

namespace App\Http\Entities;

use PHPUnit\Framework\TestCase;

// phpcs:disable Generic.Files.LineLength.TooLong

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
            [
                'https://fonts.googleapis.com',
                [
                    'href'  => 'https://fonts.gstatic.com',
                    'attributes' => 'crossorigin',
                ],
            ],
            $meta->preConnect(),
        );

        self::assertSame(
            ['https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,800;1,300;1,400;1,800&display=swap'],
            $meta->stylesheets(),
        );

        self::assertSame(
            ['https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js'],
            $meta->jsFiles(),
        );
    }
}
