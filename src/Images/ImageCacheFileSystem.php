<?php

declare(strict_types=1);

namespace App\Images;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

/**
 * @method Local getAdapter()
 */
class ImageCacheFileSystem extends Filesystem
{
}
