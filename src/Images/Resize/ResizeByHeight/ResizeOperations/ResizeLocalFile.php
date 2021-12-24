<?php

declare(strict_types=1);

namespace App\Images\Resize\ResizeByHeight\ResizeOperations;

use App\Images\ImageCacheFileSystem;
use App\Images\Resize\ImageResizeFactory;
use Gumlet\ImageResizeException;
use SplFileInfo;

use function ltrim;
use function rtrim;

class ResizeLocalFile implements ResizeOperationContract
{
    public function __construct(
        private SplFileInfo $sourceFileInfo,
        private ImageResizeFactory $imageResizeFactory,
        private ImageCacheFileSystem $imageCacheFileSystem,
    ) {
    }

    /**
     * @throws ImageResizeException
     */
    public function resize(
        string $targetFileName,
        int $pixelHeight,
    ): void {
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

        $targetInfo = new SplFileInfo($targetFileName);

        $this->imageCacheFileSystem->createDir(
            $targetInfo->getPath()
        );

        $imageResize = $this->imageResizeFactory->make(
            filename: $this->sourceFileInfo->getPathname(),
        );

        $imageResize->resizeToHeight($pixelHeight);

        $imageResize->save($finalTarget);
    }
}
