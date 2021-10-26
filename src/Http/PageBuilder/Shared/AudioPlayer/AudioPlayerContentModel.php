<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\Shared\AudioPlayer;

use function array_filter;
use function array_map;
use function array_values;
use function count;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class AudioPlayerContentModel
{
    /** @var AudioPlayerKeyValItem[] */
    private array $keyValueItems = [];

    /**
     * @param AudioPlayerKeyValItem[] $keyValueItems
     */
    public function __construct(
        private string $href,
        private string $title,
        private string $subTitle,
        private string $audioFileHref,
        private string $audioFileMimeType = 'audio/mp3',
        array $keyValueItems = [],
    ) {
        array_map(
            [$this, 'addKeyValueItem'],
            $keyValueItems,
        );
    }

    private function addKeyValueItem(AudioPlayerKeyValItem $item): void
    {
        $this->keyValueItems[] = $item;
    }

    public function href(): string
    {
        return $this->href;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function subTitle(): string
    {
        return $this->subTitle;
    }

    /**
     * @return AudioPlayerKeyValItem[]
     */
    public function keyValueItems(): array
    {
        return $this->keyValueItems;
    }

    public function hasKeyValueItems(): bool
    {
        return count($this->keyValueItems) > 0;
    }

    /**
     * @return AudioPlayerKeyValItem[]
     */
    public function getSeries(): array
    {
        return array_values(array_filter(
            $this->keyValueItems,
            static fn (
                AudioPlayerKeyValItem $i
            ) => $i->key() === 'series',
        ));
    }

    public function getFirstSeries(): ?AudioPlayerKeyValItem
    {
        $series = $this->getSeries();

        if (count($series) < 1) {
            return null;
        }

        return $this->getSeries()[0];
    }

    public function firstSeriesGuarantee(): AudioPlayerKeyValItem
    {
        return $this->getSeries()[0];
    }

    public function audioFileHref(): string
    {
        return $this->audioFileHref;
    }

    public function audioFileMimeType(): string
    {
        return $this->audioFileMimeType;
    }
}
