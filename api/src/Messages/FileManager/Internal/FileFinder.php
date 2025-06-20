<?php

declare(strict_types=1);

namespace App\Messages\FileManager\Internal;

use App\Messages\FileManager\FileRepository;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

use function is_dir;

readonly class FileFinder
{
    public function __construct(private Filesystem $filesystem)
    {
    }

    public function findFileByName(string $fileName): SplFileInfo|null
    {
        $filePath = $this->createFilePath($fileName);

        if (
            ! $this->filesystem->exists($filePath)
            || is_dir($filePath)
        ) {
            return null;
        }

        return new SplFileInfo($filePath);
    }

    public function fileExists(string $fileName): bool
    {
        return $this->findFileByName($fileName) !== null;
    }

    public function createFilePath(string $fileName): string
    {
        return FileRepository::BASE_PATH . $fileName;
    }
}
