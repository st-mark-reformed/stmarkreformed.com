<?php

declare(strict_types=1);

namespace App\Messages\FileManager;

use App\Messages\FileManager\Internal\DeleteNames;
use App\Messages\FileManager\Internal\FileFinder;
use App\Messages\FileManager\Internal\FindAllFiles;
use App\Messages\FileManager\Internal\SaveBase64FileToDisk;
use App\Persistence\Result;
use SplFileInfo;

readonly class FileRepository
{
    public const string BASE_PATH = '/uploads/audio/';

    public function __construct(
        private FileFinder $fileFinder,
        private DeleteNames $deleteNames,
        private FindAllFiles $findAllFiles,
        private SaveBase64FileToDisk $saveBase64FileToDisk,
    ) {
    }

    public function findFileByName(string $fileName): SplFileInfo|null
    {
        return $this->fileFinder->findFileByName($fileName);
    }

    public function fileExists(string $fileName): bool
    {
        return $this->fileFinder->fileExists($fileName);
    }

    public function findAllFiles(): Files
    {
        return $this->findAllFiles->find();
    }

    public function saveBase64FileToDisk(
        string $fileName,
        string $base64Data,
    ): void {
        $this->saveBase64FileToDisk->save(
            $this->fileFinder->createFilePath($fileName),
            $base64Data,
        );
    }

    public function deleteNames(FileNameCollection $fileNames): Result
    {
        return $this->deleteNames->delete($fileNames);
    }
}
