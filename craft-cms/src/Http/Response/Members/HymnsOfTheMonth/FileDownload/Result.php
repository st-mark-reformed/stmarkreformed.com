<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth\FileDownload;

class Result
{
    public function __construct(
        private bool $hasResult,
        private string $mimeType = '',
        private string $pathOnServer = '',
    ) {
    }

    public function hasResult(): bool
    {
        return $this->hasResult;
    }

    public function mimeType(): string
    {
        return $this->mimeType;
    }

    public function pathOnServer(): string
    {
        return $this->pathOnServer;
    }
}
