<?php

declare(strict_types=1);

namespace App\Images\Resize;

use Gumlet\ImageResize;
use Gumlet\ImageResizeException;

class ImageResizeFactory
{
    /**
     * @throws ImageResizeException
     */
    public function make(string $filename): ImageResize
    {
        return new ImageResize($filename);
    }
}
