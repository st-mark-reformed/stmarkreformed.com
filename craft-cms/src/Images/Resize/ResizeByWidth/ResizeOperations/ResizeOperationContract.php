<?php

declare(strict_types=1);

namespace App\Images\Resize\ResizeByWidth\ResizeOperations;

interface ResizeOperationContract
{
    public function resize(
        string $targetFileName,
        int $pixelWidth,
    ): void;
}
