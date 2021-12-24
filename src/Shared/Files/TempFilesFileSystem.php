<?php

declare(strict_types=1);

namespace App\Shared\Files;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

/**
 * @method Local getAdapter()
 */
class TempFilesFileSystem extends Filesystem
{
}
