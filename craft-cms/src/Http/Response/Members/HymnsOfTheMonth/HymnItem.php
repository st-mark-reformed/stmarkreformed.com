<?php

declare(strict_types=1);

namespace App\Http\Response\Members\HymnsOfTheMonth;

class HymnItem
{
    public function __construct(
        private string $href,
        private string $title,
        private string $slug,
        private string $hymnPsalmName,
        private string $content,
        private string|null $musicSheetFilePath,
        private array $practiceTracks,
    ) {
        array_map(
            static fn (HymnItemPracticeTrack $t) => $t,
            $practiceTracks,
        );
    }

    public function href(): string
    {
        return $this->href;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function hymnPsalmName(): string
    {
        return $this->hymnPsalmName;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function musicSheetFilePath(): string|null
    {
        return $this->musicSheetFilePath;
    }

    /** @return HymnItemPracticeTrack[] */
    public function practiceTracks(): array
    {
        return $this->practiceTracks;
    }
}
