<?php

declare(strict_types=1);

namespace App\Images\Resize\ResizeByWidth\ResizeOperations;

use App\Images\ImageCacheFileSystem;
use App\Images\Resize\ImageResizeFactory;
use App\Shared\Files\RemoteSplFileInfo;
use App\Shared\Files\TempFilesFileSystem;
use SplFileInfo;

class ResizeOperationFactory
{
    public function __construct(
        private ImageResizeFactory $imageResizeFactory,
        private TempFilesFileSystem $tempFilesFileSystem,
        private ImageCacheFileSystem $imageCacheFileSystem,
    ) {
    }

    public function make(SplFileInfo $sourceFileInfo): ResizeOperationContract
    {
        if ($sourceFileInfo instanceof RemoteSplFileInfo) {
            return new ResizeRemoteFile(
                sourceFileInfo: $sourceFileInfo,
                imageResizeFactory: $this->imageResizeFactory,
                tempFilesFileSystem: $this->tempFilesFileSystem,
                imageCacheFileSystem: $this->imageCacheFileSystem,
            );
        }

        return new ResizeLocalFile(
            sourceFileInfo: $sourceFileInfo,
            imageResizeFactory: $this->imageResizeFactory,
            imageCacheFileSystem: $this->imageCacheFileSystem,
        );
    }
}
