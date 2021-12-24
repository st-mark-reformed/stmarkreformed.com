<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\FeaturedSermonSeries;

use Twig\Markup;

class FeaturedSermonSeriesContentModel
{
    public function __construct(
        private string $headline,
        private string $seriesTitle,
        private string $seriesHref,
        private string $latestInSeriesPlayerHtml,
        private string $backgroundImageHref = '',
    ) {
    }

    public function headline(): string
    {
        return $this->headline;
    }

    public function seriesTitle(): string
    {
        return $this->seriesTitle;
    }

    public function seriesHref(): string
    {
        return $this->seriesHref;
    }

    public function latestInSeriesPlayerHtml(): Markup
    {
        return new Markup(
            $this->latestInSeriesPlayerHtml,
            'UTF-8',
        );
    }

    public function backgroundImageHref(): string
    {
        return $this->backgroundImageHref;
    }
}
