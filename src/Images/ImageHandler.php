<?php

declare(strict_types=1);

namespace App\Images;

use App\Images\Queue\PushToQueueIfNotInQueue;
use App\Images\Queue\ResizeImageByHeightQueueJob;
use App\Images\Queue\ResizeImageByWidthQueueJob;

class ImageHandler
{
    public function __construct(
        private string $urlToImageCacheDirectory,
        private FileNameCompiler $fileNameCompiler,
        private ImageCacheFileSystem $imageCacheFileSystem,
        private PushToQueueIfNotInQueue $pushToQueueIfNotInQueue,
    ) {
    }

    public function resizeToWidthExists(
        string $pathOrUrl,
        int $pixelWidth,
    ): bool {
        $fileName = $this->fileNameCompiler->forResizeToWidth(
            pathOrUrl: $pathOrUrl,
            pixelWidth: $pixelWidth,
        );

        return $this->imageCacheFileSystem->has($fileName);
    }

    public function resizeToWidthByQueue(
        string $pathOrUrl,
        int $pixelWidth,
        bool $returnOriginalIfNotExists = false
    ): ?string {
        $fileName = $this->fileNameCompiler->forResizeToWidth(
            pathOrUrl: $pathOrUrl,
            pixelWidth: $pixelWidth,
        );

        $exists = $this->imageCacheFileSystem->has($fileName);

        if (! $exists) {
            $this->pushToQueueIfNotInQueue->push(job: new ResizeImageByWidthQueueJob(
                pathOrUrl: $pathOrUrl,
                pixelWidth: $pixelWidth,
            ));

            return $returnOriginalIfNotExists ? $pathOrUrl : null;
        }

        return $this->urlToImageCacheDirectory . '/' . $fileName;
    }

    public function resizeToHeightExists(
        string $pathOrUrl,
        int $pixelHeight,
    ): bool {
        $fileName = $this->fileNameCompiler->forResizeToHeight(
            pathOrUrl: $pathOrUrl,
            pixelHeight: $pixelHeight,
        );

        return $this->imageCacheFileSystem->has($fileName);
    }

    public function resizeToHeightByQueue(
        string $pathOrUrl,
        int $pixelHeight,
        bool $returnOriginalIfNotExists = false
    ): ?string {
        $fileName = $this->fileNameCompiler->forResizeToHeight(
            pathOrUrl: $pathOrUrl,
            pixelHeight: $pixelHeight,
        );

        $exists = $this->imageCacheFileSystem->has($fileName);

        if (! $exists) {
            $this->pushToQueueIfNotInQueue->push(job: new ResizeImageByHeightQueueJob(
                pathOrUrl: $pathOrUrl,
                pixelHeight: $pixelHeight,
            ));

            return $returnOriginalIfNotExists ? $pathOrUrl : null;
        }

        return $this->urlToImageCacheDirectory . '/' . $fileName;
    }
}
