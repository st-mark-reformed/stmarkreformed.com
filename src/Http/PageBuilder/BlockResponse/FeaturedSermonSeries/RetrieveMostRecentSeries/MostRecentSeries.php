<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\FeaturedSermonSeries\RetrieveMostRecentSeries;

use App\Http\PageBuilder\Shared\AudioPlayer\AudioPlayerContentModel;

class MostRecentSeries
{
    public function __construct(
        private string $seriesTitle,
        private string $seriesUrl,
        private AudioPlayerContentModel $playerContentModel,
    ) {
    }

    public function seriesTitle(): string
    {
        return $this->seriesTitle;
    }

    public function seriesUrl(): string
    {
        return $this->seriesUrl;
    }

    public function playerContentModel(): AudioPlayerContentModel
    {
        return $this->playerContentModel;
    }
}
