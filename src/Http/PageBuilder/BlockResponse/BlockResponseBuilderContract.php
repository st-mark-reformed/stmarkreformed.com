<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse;

use App\Http\PageBuilder\BlockResponse\BasicBlock\BasicBlock;
use App\Http\PageBuilder\BlockResponse\FeaturedSermonSeries\FeaturedSermonSeries;
use App\Http\PageBuilder\BlockResponse\ImageContentCta\ImageContentCta;
use App\Http\PageBuilder\BlockResponse\LatestGalleries\LatestGalleries;
use App\Http\PageBuilder\BlockResponse\Leadership\Leadership;
use App\Http\PageBuilder\BlockResponse\SimpleCta\SimpleCta;
use App\Http\PageBuilder\BlockResponse\StripePaymentForm\StripePaymentForm;
use craft\elements\MatrixBlock;

interface BlockResponseBuilderContract
{
    public const BLOCK_TYPE_MAP = [
        'basicBlock' => BasicBlock::class,
        'featuredSermonSeries' => FeaturedSermonSeries::class,
        'imageContentCta' => ImageContentCta::class,
        'latestGalleries' => LatestGalleries::class,
        'leadership' => Leadership::class,
        'simpleCta' => SimpleCta::class,
        'stripePaymentForm' => StripePaymentForm::class,
    ];

    /**
     * @phpstan-ignore-next-line
     */
    public function buildResponse(MatrixBlock $matrixBlock): string;
}
