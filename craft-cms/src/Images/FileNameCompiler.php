<?php

declare(strict_types=1);

namespace App\Images;

use function md5;
use function serialize;

class FileNameCompiler
{
    public function __construct(
        private SourceFileRetriever $sourceFileRetriever,
    ) {
    }

    public function forResizeToWidth(
        string $pathOrUrl,
        int $pixelWidth,
    ): string {
        $sourceFile = $this->sourceFileRetriever->retrieveInfo(
            pathOrUrl:  $pathOrUrl,
        );

        $hash = md5(serialize([
            'resizeToWidth',
            $pathOrUrl,
            $pixelWidth,
            $sourceFile->getSize(),
        ]));

        return 'resize-to-width/' . $hash . '/' . $sourceFile->getBasename();
    }

    public function forResizeToHeight(
        string $pathOrUrl,
        int $pixelHeight,
    ): string {
        $sourceFile = $this->sourceFileRetriever->retrieveInfo(
            pathOrUrl:  $pathOrUrl,
        );

        $hash = md5(serialize([
            'resizeToHeight',
            $pathOrUrl,
            $pixelHeight,
            $sourceFile->getSize(),
        ]));

        return 'resize-to-height/' . $hash . '/' . $sourceFile->getBasename();
    }
}
