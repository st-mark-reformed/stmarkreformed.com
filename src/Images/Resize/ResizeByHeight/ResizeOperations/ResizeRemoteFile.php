<?php

declare(strict_types=1);

namespace App\Images\Resize\ResizeByHeight\ResizeOperations;

use App\Images\ImageCacheFileSystem;
use App\Images\Resize\ImageResizeFactory;
use App\Shared\Files\RemoteSplFileInfo;
use App\Shared\Files\TempFilesFileSystem;
use Gumlet\ImageResizeException;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use SplFileInfo;

use function ltrim;
use function rtrim;

class ResizeRemoteFile implements ResizeOperationContract
{
    public function __construct(
        private RemoteSplFileInfo $sourceFileInfo,
        private ImageResizeFactory $imageResizeFactory,
        private TempFilesFileSystem $tempFilesFileSystem,
        private ImageCacheFileSystem $imageCacheFileSystem,
    ) {
    }

    /**
     * @throws ImageResizeException
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    public function resize(
        string $targetFileName,
        int $pixelHeight,
    ): void {
        $cacheDir = rtrim(
            (string) $this->tempFilesFileSystem
                ->getAdapter()
                ->getPathPrefix(),
            '/',
        );

        $fullCacheTarget = $cacheDir . '/' . ltrim(
            $targetFileName,
            '/',
        );

        $finalDir = rtrim(
            (string) $this->imageCacheFileSystem
                ->getAdapter()
                ->getPathPrefix(),
            '/',
        );

        $finalTarget = $finalDir . '/' . ltrim(
            $targetFileName,
            '/',
        );

        if ($this->tempFilesFileSystem->has($targetFileName)) {
            $this->tempFilesFileSystem->delete($targetFileName);
        }

        $this->tempFilesFileSystem->write(
            $targetFileName,
            $this->sourceFileInfo->getContent(),
        );

        $targetInfo = new SplFileInfo($targetFileName);

        $this->imageCacheFileSystem->createDir(
            $targetInfo->getPath()
        );

        $imageResize = $this->imageResizeFactory->make(
            filename: $fullCacheTarget,
        );

        $imageResize->resizeToHeight($pixelHeight);

        $imageResize->save($finalTarget);

        $this->tempFilesFileSystem->delete($targetFileName);

        $this->tempFilesFileSystem->deleteDir($targetInfo->getPath());
    }
}
