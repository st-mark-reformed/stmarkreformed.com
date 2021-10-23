<?php

declare(strict_types=1);

namespace App\Images\Resize\ResizeByHeight\ResizeOperations;

interface ResizeOperationContract
{
    public function resize(
        string $targetFileName,
        int $pixelHeight,
    ): void;
}
