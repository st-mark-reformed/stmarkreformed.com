<?php

declare(strict_types=1);

namespace App\Messages\FileManager;

use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

readonly class FileRepository
{
    public const string BASE_PATH = '/uploads/audio/';

    public function __construct(
        private Filesystem $filesystem,
        private SaveBase64FileToDisk $saveBase64FileToDisk,
    ) {
    }

    private function createFilePath(string $fileName): string
    {
        return self::BASE_PATH . $fileName;
    }

    public function findFileByName(string $fileName): SplFileInfo|null
    {
        $filePath = $this->createFilePath($fileName);

        if (! $this->filesystem->exists($filePath)) {
            return null;
        }

        return new SplFileInfo($filePath);
    }

    public function fileExists(string $fileName): bool
    {
        return $this->findFileByName($fileName) !== null;
    }

    public function saveBase64FileToDisk(
        string $fileName,
        string $base64Data,
    ): void {
        $this->saveBase64FileToDisk->save(
            $this->createFilePath($fileName),
            $base64Data,
        );
    }
}
