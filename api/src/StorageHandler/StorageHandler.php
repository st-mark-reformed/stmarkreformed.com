<?php

declare(strict_types=1);

namespace App\StorageHandler;

use Symfony\Component\Filesystem\Filesystem;

use function dirname;

readonly class StorageHandler
{
    private string $storagePath;

    public function __construct(private Filesystem $filesystem)
    {
        $this->storagePath = dirname(__DIR__, 2) . '/storage/';
    }

    public function fileExists(string $filename): bool
    {
        return $this->filesystem->exists(
            $this->storagePath . $filename,
        );
    }

    public function write(string $filename, string $content): void
    {
        $this->filesystem->dumpFile(
            $this->storagePath . $filename,
            $content,
        );
    }
}
