<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse;

use craft\elements\MatrixBlock;

interface BlockResponseBuilderContract
{
    /**
     * @phpstan-ignore-next-line
     */
    public function buildResponse(MatrixBlock $matrixBlock): string;
}
