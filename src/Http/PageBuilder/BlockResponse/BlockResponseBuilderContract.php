<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse;

use App\Http\PageBuilder\BlockResponse\BasicBlock\BasicBlock;
use App\Http\PageBuilder\BlockResponse\FeaturedSermonSeries\FeaturedSermonSeries;
use App\Http\PageBuilder\BlockResponse\ImageContentCta\ImageContentCta;
use App\Http\PageBuilder\BlockResponse\LatestGalleries\LatestGalleries;
use App\Http\PageBuilder\BlockResponse\SimpleCta\SimpleCta;
use craft\elements\MatrixBlock;

interface BlockResponseBuilderContract
{
    public const BLOCK_TYPE_MAP = [
        'basicBlock' => BasicBlock::class,
        'featuredSermonSeries' => FeaturedSermonSeries::class,
        'imageContentCta' => ImageContentCta::class,
        'latestGalleries' => LatestGalleries::class,
        'simpleCta' => SimpleCta::class,
    ];

    /**
     * @phpstan-ignore-next-line
     */
    public function buildResponse(MatrixBlock $matrixBlock): string;
}
