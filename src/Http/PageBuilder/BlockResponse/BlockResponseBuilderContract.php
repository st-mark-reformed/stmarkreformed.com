<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse;

use App\Http\PageBuilder\BlockResponse\BasicBlock\BasicBlock;
use App\Http\PageBuilder\BlockResponse\BasicEntryBlock\BasicEntryBlock;
use App\Http\PageBuilder\BlockResponse\ContactForm\ContactForm;
use App\Http\PageBuilder\BlockResponse\FeaturedSermonSeries\FeaturedSermonSeries;
use App\Http\PageBuilder\BlockResponse\ImageContentCta\ImageContentCta;
use App\Http\PageBuilder\BlockResponse\ImageEntryBlock\ImageEntryBlock;
use App\Http\PageBuilder\BlockResponse\LatestGalleries\LatestGalleries;
use App\Http\PageBuilder\BlockResponse\LatestNews\LatestNews;
use App\Http\PageBuilder\BlockResponse\Leadership\Leadership;
use App\Http\PageBuilder\BlockResponse\SimpleCta\SimpleCta;
use App\Http\PageBuilder\BlockResponse\StripePaymentForm\StripePaymentForm;
use App\Http\PageBuilder\BlockResponse\UpcomingEvents\UpcomingEvents;
use craft\elements\MatrixBlock;

interface BlockResponseBuilderContract
{
    public const BLOCK_TYPE_MAP = [
        'basicBlock' => BasicBlock::class,
        'basicEntryBlock' => BasicEntryBlock::class,
        'contactForm' => ContactForm::class,
        'featuredSermonSeries' => FeaturedSermonSeries::class,
        'imageContentCta' => ImageContentCta::class,
        'imageEntryBlock' => ImageEntryBlock::class,
        'latestGalleries' => LatestGalleries::class,
        'latestNews' => LatestNews::class,
        'leadership' => Leadership::class,
        'simpleCta' => SimpleCta::class,
        'stripePaymentForm' => StripePaymentForm::class,
        'upcomingEvents' => UpcomingEvents::class,
    ];

    /**
     * @phpstan-ignore-next-line
     */
    public function buildResponse(MatrixBlock $matrixBlock): string;
}
