<?php

declare(strict_types=1);

use App\Shared\Files\PublicDirectoryFileSystem;
use App\Shared\Files\RootDirectoryFileSystem;
use App\Shared\Files\TempFilesFileSystem;
use League\Flysystem\Adapter\Local;

/** @psalm-suppress UndefinedConstant */
return [
    PublicDirectoryFileSystem::class => static function (): PublicDirectoryFileSystem {
        return new PublicDirectoryFileSystem(
            new Local(CRAFT_BASE_PATH . '/public'),
        );
    },
    TempFilesFileSystem::class => static function (): TempFilesFileSystem {
        $path = CRAFT_BASE_PATH . '/storage/runtime/temp';

        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return new TempFilesFileSystem(
            new Local($path),
        );
    },
    RootDirectoryFileSystem::class => static function (): RootDirectoryFileSystem {
        return new RootDirectoryFileSystem(new Local('/'));
    },
];
