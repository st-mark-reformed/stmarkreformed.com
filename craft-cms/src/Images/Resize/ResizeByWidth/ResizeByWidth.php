<?php

declare(strict_types=1);

namespace App\Images\Resize\ResizeByWidth;

use App\Images\FileNameCompiler;
use App\Images\ImageCacheFileSystem;
use App\Images\Resize\ResizeByWidth\ResizeOperations\ResizeOperationFactory;
use App\Images\SourceFileRetriever;
use BuzzingPixel\StaticCache\CacheApi\CacheApiContract;
use Spatie\ImageOptimizer\OptimizerChain;

use function ltrim;
use function rtrim;

class ResizeByWidth
{
    public function __construct(
        private OptimizerChain $optimizerChain,
        private CacheApiContract $staticCacheApi,
        private FileNameCompiler $fileNameCompiler,
        private SourceFileRetriever $sourceFileRetriever,
        private ImageCacheFileSystem $imageCacheFileSystem,
        private ResizeOperationFactory $resizeOperationFactory,
    ) {
    }

    public function resize(
        string $pathOrUrl,
        int $pixelWidth,
    ): void {
        $sourceFileInfo = $this->sourceFileRetriever->retrieveInfo(
            pathOrUrl: $pathOrUrl,
        );

        $targetFileName = $this->fileNameCompiler->forResizeToWidth(
            pathOrUrl: $pathOrUrl,
            pixelWidth: $pixelWidth,
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

        $this->resizeOperationFactory->make(
            sourceFileInfo: $sourceFileInfo,
        )->resize(
            targetFileName: $targetFileName,
            pixelWidth: $pixelWidth,
        );

        $this->optimizerChain->optimize($finalTarget);

        $this->staticCacheApi->clearAllCache();
    }
}
