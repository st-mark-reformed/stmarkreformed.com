<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse;

use App\Http\PageBuilder\BlockResponse\FeaturedSermonSeries\FeaturedSermonSeries;
use App\Http\PageBuilder\BlockResponse\ImageContentCta\ImageContentCta;
use App\Http\PageBuilder\BlockResponse\LatestGalleries\LatestGalleries;
use craft\elements\MatrixBlock;

interface BlockResponseBuilderContract
{
    public const BLOCK_TYPE_MAP = [
        'imageContentCta' => ImageContentCta::class,
        'latestGalleries' => LatestGalleries::class,
        'featuredSermonSeries' => FeaturedSermonSeries::class,
    ];

    /**
     * @phpstan-ignore-next-line
     */
    public function buildResponse(MatrixBlock $matrixBlock): string;
}
