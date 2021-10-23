<?php

declare(strict_types=1);

namespace App\Images\Resize;

use Gumlet\ImageResizeException;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedArrayAccess
 */
class ImageResizeFactoryTest extends TestCase
{
    /**
     * @throws ImageResizeException
     */
    public function testMake(): void
    {
        $factory = new ImageResizeFactory();

        $factory->make(__DIR__ . '/1x1.png');

        self::assertTrue(true);
    }
}
