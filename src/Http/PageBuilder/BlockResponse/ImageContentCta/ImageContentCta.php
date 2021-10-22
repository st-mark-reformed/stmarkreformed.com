<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ImageContentCta;

use App\Http\PageBuilder\BlockResponse\BlockResponseBuilderContract;
use craft\elements\MatrixBlock;

use function dd;

class ImageContentCta implements BlockResponseBuilderContract
{
    /**
     * @phpstan-ignore-next-line
     */
    public function buildResponse(MatrixBlock $matrixBlock): string
    {
        // TODO: Implement buildResponse() method.
        dd($matrixBlock);

        return 'TODO: Implement buildResponse() method.';
    }
}
